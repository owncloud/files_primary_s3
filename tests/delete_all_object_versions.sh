#!/bin/sh

bucket=$1
endpointUrl=https://s3-b.isv.scality.com
set -e

echo "Removing all versions from $bucket"

versions=$(aws --endpoint-url $endpointUrl s3api list-object-versions --bucket $bucket | jq '.Versions')
markers=$(aws --endpoint-url $endpointUrl s3api list-object-versions --bucket $bucket | jq '.DeleteMarkers')
count=$(($(echo "$versions" | jq 'length') - 1))

if [ $count -gt -1 ]; then
  echo "Removing files. Need to delete $count files"
  for i in $(seq 0 $count); do
    key=$(echo "$versions" | jq .[$i].Key | sed -e 's/\"//g')
    versionId=$(echo "$versions" | jq .[$i].VersionId | sed -e 's/\"//g')
    cmd="aws --endpoint-url $endpointUrl s3api delete-object --bucket $bucket --key $key --version-id $versionId"
    $cmd > /dev/null &
  done
fi

count=$(($(echo "$markers" | jq 'length') - 1))
if [ $count -gt -1 ]; then
  echo "Removing delete markers. Need to delete $count markers"
  for i in $(seq 0 $count); do
    key=$(echo "$markers" | jq .[$i].Key | sed -e 's/\"//g')
    versionId=$(echo "$markers" | jq .[$i].VersionId | sed -e 's/\"//g')
    cmd="aws --endpoint-url $endpointUrl s3api delete-object --bucket $bucket --key $key --version-id $versionId"
    $cmd > /dev/null &
  done
fi
