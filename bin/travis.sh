#!/bin/sh

composer -n install
mysql -e 'create database symf_test;'
cp app/config/parameters.yml.dist app/config/parameters.yml

echo '\t testdb_host: 127.0.0.1' >> app/config/parameters.yml
echo '\t testdb_user: travis' >> app/config/parameters.yml
echo '\t testdb_name: symf_test' >> app/config/parameters.yml   
