<?php

namespace App\Http\Controllers;

use App\Models\User;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ElasticController extends Controller
{
    public function config()
    {
        $url = env('ELASTICSEARCH_HOST');
        $username = env('ELASTICSEARCH_USERNAME');
        $password = env('ELASTICSEARCH_PASSWORD');

        $response = Http::withBasicAuth($username, $password)
            ->withoutVerifying()
            ->get($url);
        return $response->json();
    }

    public function createApiKey()
    {

        $url = env('ELASTICSEARCH_HOST') . '/_security/api_key';
        $username = env('ELASTICSEARCH_USERNAME');
        $password = env('ELASTICSEARCH_PASSWORD');


        $data = [
            'name' => 'my-api-key',
            'expiration' => '1d', // زمان انقضا 1 روز
            'role_descriptors' => [
                'my_custom_role' => [
                    'cluster' => ['all'],
                    'index' => [
                        [
                            'names' => ['*'],
                            'privileges' => ['read']
                        ]
                    ]
                ]
            ]
        ];


        $response = Http::withBasicAuth($username, $password)
            ->withoutVerifying()
            ->post($url, $data);

        if ($response->successful()) {
            return response()->json([
                'api_key' => $response->json()['id'],
                'api_key_secret' => $response->json()['api_key'],
            ], 200);
        } else {
            return response()->json([
                'error' => 'Unable to create API key.',
                'message' => $response->body()
            ], 500);
        }
    }

    public function createServiceToken()
    {
        // URL اتصال به Elasticsearch
        $url = env('ELASTICSEARCH_HOST') . '/_security/service/token';
        $username = env('ELASTICSEARCH_USERNAME');
        $password = env('ELASTICSEARCH_PASSWORD');

        // داده‌هایی که باید ارسال شوند
        $data = [
            'name' => 'my-service-token',  // نام توکن
            'expiration' => '1d',  // زمان انقضای توکن (یک روز)
            'roles' => ['admin'],  // نقش‌هایی که به توکن اختصاص داده می‌شود
        ];

        // ارسال درخواست به Elasticsearch برای ایجاد Service Token با متد GET
        $response = Http::withBasicAuth($username, $password)  // احراز هویت با نام کاربری و رمز عبور
        ->withoutVerifying()  // غیرفعال کردن تایید SSL برای اتصالات خود امضا شده
        ->get($url, $data);  // ارسال درخواست GET به آدرس بالا

        // بررسی وضعیت پاسخ
        if ($response->successful()) {
            return response()->json([
                'service_token' => $response->json()['token'],  // توکن سرویسی که برگشت داده شده
            ], 200);
        } else {
            // در صورت بروز خطا، پیام خطا را برمی‌گرداند
            return response()->json([
                'error' => 'Unable to create service token.',
                'message' => $response->body(),
                'status_code' => $response->status(),  // کد وضعیت HTTP
            ], 500);
        }
    }


    public function search(Request $request)
    {
        $query = $request->query('search');

        if (!$query) {
            return response()->json(['error' => 'Search query is required'], 400);
        }

        $client = ClientBuilder::create()
            ->setHosts([env('ELASTICSEARCH_HOST')])
            ->setBasicAuthentication(env('ELASTICSEARCH_USERNAME'), env('ELASTICSEARCH_PASSWORD'))
            ->setSSLVerification(false)
            ->build();

        $params = [
            'index' => 'users',
            'body' => [
                'query' => [
                    'wildcard' => [
                        'name' => '*' . $query . '*'
                    ]
                ]
//                'query' => [
//        'match' => [
//            'name' => $query
//        ]
//    ]


//            'range' => [
//        'date' => [
//            'gte' => '2021-01-01',
//            'lte' => '2022-01-01'
//        ]
//    ]


//            'fuzzy' => [
//        'name' => 'jon'
//    ]


//            'wildcard' => [
//        'name' => 'j*'
//    ]
            
            ]
        ];

        $response = $client->search($params);

        $hits = $response['hits']['hits'] ?? [];

        return response()->json($hits);
    }


//    public function syncData(Request $request)
//    {
//
//        $data = User::all();
//        $client = ClientBuilder::create()
//            ->setHosts([env('ELASTICSEARCH_HOST')])
//            ->setBasicAuthentication(env('ELASTICSEARCH_USERNAME'), env('ELASTICSEARCH_PASSWORD'))
//            ->setSSLVerification(false)
//            ->build();
//        foreach ($data as $item) {
//            $params = [
//                'index' => 'users',
//                'id' => $item->id,
//                'body' => [
//                    'doc' => [
//                        'name' => $item->name,
//                        'email' => $item->email,
//                    ],
//                    'doc_as_upsert' => true,
//                ]
//            ];
//
//            $client->update($params);
//        }
//        return response()->json(['message' => 'Data synchronized with Elasticsearch']);
//
//    }
    public function syncData()
    {
        $data = User::all();
        $client = ClientBuilder::create()
            ->setHosts([env('ELASTICSEARCH_HOST')])
            ->setBasicAuthentication(env('ELASTICSEARCH_USERNAME'), env('ELASTICSEARCH_PASSWORD'))
            ->setSSLVerification(false)
            ->build();
        $userIdsInDb = User::pluck('id')->toArray();

        $params = [
            'index' => 'users',
            'body' => [
                'query' => [
                    'bool' => [
                        'must_not' => [
                            'terms' => [
                                'id' => $userIdsInDb,
                            ]
                        ]
                    ]
                ]
            ]
        ];


        $response = $client->deleteByQuery($params);


        foreach ($data as $item) {
            $params = [
                'index' => 'users',
                'id' => $item->id,
                'body' => [
                    'doc' => [
                        'name' => $item->name,
                        'email' => $item->email,
                    ],
                    'doc_as_upsert' => true,
                ]
            ];

            $client->update($params);
        }

        return response()->json(['message' => 'Data synchronized with Elasticsearch']);
    }

    public function store(Request $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);
        return response()->json([$user]);
    }
}
