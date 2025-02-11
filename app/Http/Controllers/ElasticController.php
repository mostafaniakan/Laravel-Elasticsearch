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
        $username = "elastic";
        $password = env('ELASTICSEARCH_KEY');

        $response = Http::withBasicAuth($username, $password)
            ->withoutVerifying()
            ->get($url);
        return $response->json();
    }

    public function search(Request $request)
    {
        $query = $request->query('search');

        if (!$query) {
            return response()->json(['error' => 'Search query is required'], 400);
        }

        $client = ClientBuilder::create()
            ->setHosts([env('ELASTICSEARCH_HOST')])
            ->setBasicAuthentication('elastic', env('ELASTICSEARCH_KEY'))
            ->setSSLVerification(false)
            ->build();

        $params = [
            'index' => 'users',
            'body' => [
                'query' => [
                    'term' => [
                        'name.keyword' => $query
                    ]
                ]
//                'query' => [
//        'match' => [
//            'name' => $query
//        ]
//    ]
            ]
        ];

        $response = $client->search($params);

        $hits = $response['hits']['hits'] ?? [];

        return response()->json($hits);
    }


    public function syncData(Request $request)
    {

        $data = User::all();
        $client = ClientBuilder::create()
            ->setHosts([env('ELASTICSEARCH_HOST')])
            ->setBasicAuthentication('elastic', env('ELASTICSEARCH_KEY'))
            ->setSSLVerification(false)
            ->build();
        foreach ($data as $item) {
            $params = [
                'index' => 'users', // نام ایندکس
                'id' => $item->id,     // شناسه مستند
                'body' => [
                    'name' => $item->name,  // فیلدهای مورد نظر برای ذخیره‌سازی
                    'email' => $item->email,
                ]
            ];

            $client->index($params); //
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
