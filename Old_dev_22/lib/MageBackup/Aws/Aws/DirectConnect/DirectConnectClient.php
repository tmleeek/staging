<?php
namespace MageBackup\Aws\DirectConnect;

use MageBackup\Aws\AwsClient;

/**
 * This client is used to interact with the **AWS Direct Connect** service.
 *
 * @method \MageBackup\Aws\Result allocateConnectionOnInterconnect(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise allocateConnectionOnInterconnectAsync(array $args = [])
 * @method \MageBackup\Aws\Result allocatePrivateVirtualInterface(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise allocatePrivateVirtualInterfaceAsync(array $args = [])
 * @method \MageBackup\Aws\Result allocatePublicVirtualInterface(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise allocatePublicVirtualInterfaceAsync(array $args = [])
 * @method \MageBackup\Aws\Result confirmConnection(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise confirmConnectionAsync(array $args = [])
 * @method \MageBackup\Aws\Result confirmPrivateVirtualInterface(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise confirmPrivateVirtualInterfaceAsync(array $args = [])
 * @method \MageBackup\Aws\Result confirmPublicVirtualInterface(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise confirmPublicVirtualInterfaceAsync(array $args = [])
 * @method \MageBackup\Aws\Result createConnection(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createConnectionAsync(array $args = [])
 * @method \MageBackup\Aws\Result createInterconnect(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createInterconnectAsync(array $args = [])
 * @method \MageBackup\Aws\Result createPrivateVirtualInterface(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createPrivateVirtualInterfaceAsync(array $args = [])
 * @method \MageBackup\Aws\Result createPublicVirtualInterface(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise createPublicVirtualInterfaceAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteConnection(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteConnectionAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteInterconnect(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteInterconnectAsync(array $args = [])
 * @method \MageBackup\Aws\Result deleteVirtualInterface(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise deleteVirtualInterfaceAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeConnections(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeConnectionsAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeConnectionsOnInterconnect(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeConnectionsOnInterconnectAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeInterconnects(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeInterconnectsAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeLocations(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeLocationsAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeVirtualGateways(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeVirtualGatewaysAsync(array $args = [])
 * @method \MageBackup\Aws\Result describeVirtualInterfaces(array $args = [])
 * @method \MageBackup\GuzzleHttp\Promise\Promise describeVirtualInterfacesAsync(array $args = [])
 */
class DirectConnectClient extends AwsClient {}
