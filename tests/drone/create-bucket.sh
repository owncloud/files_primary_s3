#!/usr/bin/env bash
# ceph in docker starts up OK, but then sometimes takes time to configure
# CEPH_DEMO_UID. If we try to create the bucket too early, then we get:
#   Client error response [url] http://ceph/OWNCLOUD [status code]
#   403 [reason phrase] Forbidden

# Loop trying to create the bucket until success or so long that it is likely
# there is some other problem.
for i in {1..10}
do
    php ./occ s3:create-bucket OWNCLOUD --accept-warning
    if [ $? -eq 0 ]
    then
        break
    fi
    echo "create-bucket failed. Maybe the ceph account is not ready yet."
    echo "waiting 10 seconds..."
    sleep 10
done
