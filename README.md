<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

ElasticSearch

First Download elasticsearch

command: sudo xattr -r -d com.apple.quarantine jdk.app

./bin/elasticsearch

- Roune Kibana
 ./bin/kibana


- Create New Token:
sudo bin/elasticsearch-create-enrollment-token --scope node

✅ Elasticsearch security features have been automatically configured!
✅ Authentication is enabled and cluster connections are encrypted.

ℹ️  Password for the elastic user (reset with `bin/elasticsearch-reset-password -u elastic`):
  ZG1Rs_CQnw3NC-=AApQZ

ℹ️  HTTP CA certificate SHA-256 fingerprint:
  26429ec12afcb909a5131978549dfaef6a81662c7724aaa8b6096fd4ba5cf51a

ℹ️  Configure Kibana to use this cluster:
• Run Kibana and click the configuration link in the terminal when Kibana starts.
• Copy the following enrollment token and paste it into Kibana in your browser (valid for the next 30 minutes):
  eyJ2ZXIiOiI4LjE0LjAiLCJhZHIiOlsiMTkyLjE2OC40My44NTo5MjAwIl0sImZnciI6IjI2NDI5ZWMxMmFmY2I5MDlhNTEzMTk3ODU0OWRmYWVmNmE4MTY2MmM3NzI0YWFhOGI2MDk2ZmQ0YmE1Y2Y1MWEiLCJrZXkiOiIxaS1DODVRQlNNdVNzanJTUklKWTpHODZIRDN4c1M2Nk9SbVhrN2UyNGdnIn0=

ℹ️  Configure other nodes to join this cluster:
• On this node:
  ⁃ Create an enrollment token with `bin/elasticsearch-create-enrollment-token -s node`.
  ⁃ Uncomment the transport.host setting at the end of config/elasticsearch.yml.
  ⁃ Restart Elasticsearch.
• On other nodes:
  ⁃ Start Elasticsearch with `bin/elasticsearch --enrollment-token <token>`, using the enrollment token that you generated.