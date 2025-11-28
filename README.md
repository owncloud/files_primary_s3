# files_primary_s3
📦 S3 compatible Storage

For its benefits over traditional file system storage, object storage has become more and more popular. Speaking simply object storages split files into parts of the same size and store them including the metadata to assemble these objects to files. In contrast to file system storage this enables infinite scalability to cope for an exponentially growing amount of data. Furthermore, object storage systems like CEPH or Scality RING provide built-in features for automatic data replication, redundancy/high availability and even geo-distribution which are necessities for professional production environments. For enterprises object storage systems can reduce maintenance efforts significantly and offer huge cost savings compared to other storage systems. 

This extension is the successor of the [ownCloud Objectstore App](https://marketplace.owncloud.com/apps/objectstore). It enables ownCloud Server to communicate with the widely spread S3 protocol (S3 HTTP API) to use object storage as it's primary storage location.

**Supported features**
- S3 Multi-part upload (enables uploading files > 5 GB)
- S3 Versioning
