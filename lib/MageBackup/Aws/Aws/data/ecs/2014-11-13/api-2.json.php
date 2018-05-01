<?php
// This file was auto-generated from sdk-root/src/data/ecs/2014-11-13/api-2.json
return [ 'version' => '2.0', 'metadata' => [ 'apiVersion' => '2014-11-13', 'endpointPrefix' => 'ecs', 'jsonVersion' => '1.1', 'protocol' => 'json', 'serviceAbbreviation' => 'Amazon ECS', 'serviceFullName' => 'Amazon EC2 Container Service', 'signatureVersion' => 'v4', 'targetPrefix' => 'AmazonEC2ContainerServiceV20141113', ], 'operations' => [ 'CreateCluster' => [ 'name' => 'CreateCluster', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'CreateClusterRequest', ], 'output' => [ 'shape' => 'CreateClusterResponse', ], 'errors' => [ [ 'shape' => 'ServerException', ], [ 'shape' => 'ClientException', ], [ 'shape' => 'InvalidParameterException', ], ], ], 'CreateService' => [ 'name' => 'CreateService', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'CreateServiceRequest', ], 'output' => [ 'shape' => 'CreateServiceResponse', ], 'errors' => [ [ 'shape' => 'ServerException', ], [ 'shape' => 'ClientException', ], [ 'shape' => 'InvalidParameterException', ], [ 'shape' => 'ClusterNotFoundException', ], ], ], 'DeleteCluster' => [ 'name' => 'DeleteCluster', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DeleteClusterRequest', ], 'output' => [ 'shape' => 'DeleteClusterResponse', ], 'errors' => [ [ 'shape' => 'ServerException', ], [ 'shape' => 'ClientException', ], [ 'shape' => 'InvalidParameterException', ], [ 'shape' => 'ClusterNotFoundException', ], [ 'shape' => 'ClusterContainsContainerInstancesException', ], [ 'shape' => 'ClusterContainsServicesException', ], ], ], 'DeleteService' => [ 'name' => 'DeleteService', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DeleteServiceRequest', ], 'output' => [ 'shape' => 'DeleteServiceResponse', ], 'errors' => [ [ 'shape' => 'ServerException', ], [ 'shape' => 'ClientException', ], [ 'shape' => 'InvalidParameterException', ], [ 'shape' => 'ClusterNotFoundException', ], [ 'shape' => 'ServiceNotFoundException', ], ], ], 'DeregisterContainerInstance' => [ 'name' => 'DeregisterContainerInstance', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DeregisterContainerInstanceRequest', ], 'output' => [ 'shape' => 'DeregisterContainerInstanceResponse', ], 'errors' => [ [ 'shape' => 'ServerException', ], [ 'shape' => 'ClientException', ], [ 'shape' => 'InvalidParameterException', ], [ 'shape' => 'ClusterNotFoundException', ], ], ], 'DeregisterTaskDefinition' => [ 'name' => 'DeregisterTaskDefinition', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DeregisterTaskDefinitionRequest', ], 'output' => [ 'shape' => 'DeregisterTaskDefinitionResponse', ], 'errors' => [ [ 'shape' => 'ServerException', ], [ 'shape' => 'ClientException', ], [ 'shape' => 'InvalidParameterException', ], ], ], 'DescribeClusters' => [ 'name' => 'DescribeClusters', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DescribeClustersRequest', ], 'output' => [ 'shape' => 'DescribeClustersResponse', ], 'errors' => [ [ 'shape' => 'ServerException', ], [ 'shape' => 'ClientException', ], [ 'shape' => 'InvalidParameterException', ], ], ], 'DescribeContainerInstances' => [ 'name' => 'DescribeContainerInstances', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DescribeContainerInstancesRequest', ], 'output' => [ 'shape' => 'DescribeContainerInstancesResponse', ], 'errors' => [ [ 'shape' => 'ServerException', ], [ 'shape' => 'ClientException', ], [ 'shape' => 'InvalidParameterException', ], [ 'shape' => 'ClusterNotFoundException', ], ], ], 'DescribeServices' => [ 'name' => 'DescribeServices', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DescribeServicesRequest', ], 'output' => [ 'shape' => 'DescribeServicesResponse', ], 'errors' => [ [ 'shape' => 'ServerException', ], [ 'shape' => 'ClientException', ], [ 'shape' => 'InvalidParameterException', ], [ 'shape' => 'ClusterNotFoundException', ], ], ], 'DescribeTaskDefinition' => [ 'name' => 'DescribeTaskDefinition', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DescribeTaskDefinitionRequest', ], 'output' => [ 'shape' => 'DescribeTaskDefinitionResponse', ], 'errors' => [ [ 'shape' => 'ServerException', ], [ 'shape' => 'ClientException', ], [ 'shape' => 'InvalidParameterException', ], ], ], 'DescribeTasks' => [ 'name' => 'DescribeTasks', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DescribeTasksRequest', ], 'output' => [ 'shape' => 'DescribeTasksResponse', ], 'errors' => [ [ 'shape' => 'ServerException', ], [ 'shape' => 'ClientException', ], [ 'shape' => 'InvalidParameterException', ], [ 'shape' => 'ClusterNotFoundException', ], ], ], 'DiscoverPollEndpoint' => [ 'name' => 'DiscoverPollEndpoint', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DiscoverPollEndpointRequest', ], 'output' => [ 'shape' => 'DiscoverPollEndpointResponse', ], 'errors' => [ [ 'shape' => 'ServerException', ], [ 'shape' => 'ClientException', ], ], ], 'ListClusters' => [ 'name' => 'ListClusters', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'ListClustersRequest', ], 'output' => [ 'shape' => 'ListClustersResponse', ], 'errors' => [ [ 'shape' => 'ServerException', ], [ 'shape' => 'ClientException', ], [ 'shape' => 'InvalidParameterException', ], ], ], 'ListContainerInstances' => [ 'name' => 'ListContainerInstances', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'ListContainerInstancesRequest', ], 'output' => [ 'shape' => 'ListContainerInstancesResponse', ], 'errors' => [ [ 'shape' => 'ServerException', ], [ 'shape' => 'ClientException', ], [ 'shape' => 'InvalidParameterException', ], [ 'shape' => 'ClusterNotFoundException', ], ], ], 'ListServices' => [ 'name' => 'ListServices', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'ListServicesRequest', ], 'output' => [ 'shape' => 'ListServicesResponse', ], 'errors' => [ [ 'shape' => 'ServerException', ], [ 'shape' => 'ClientException', ], [ 'shape' => 'InvalidParameterException', ], [ 'shape' => 'ClusterNotFoundException', ], ], ], 'ListTaskDefinitionFamilies' => [ 'name' => 'ListTaskDefinitionFamilies', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'ListTaskDefinitionFamiliesRequest', ], 'output' => [ 'shape' => 'ListTaskDefinitionFamiliesResponse', ], 'errors' => [ [ 'shape' => 'ServerException', ], [ 'shape' => 'ClientException', ], [ 'shape' => 'InvalidParameterException', ], ], ], 'ListTaskDefinitions' => [ 'name' => 'ListTaskDefinitions', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'ListTaskDefinitionsRequest', ], 'output' => [ 'shape' => 'ListTaskDefinitionsResponse', ], 'errors' => [ [ 'shape' => 'ServerException', ], [ 'shape' => 'ClientException', ], [ 'shape' => 'InvalidParameterException', ], ], ], 'ListTasks' => [ 'name' => 'ListTasks', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'ListTasksRequest', ], 'output' => [ 'shape' => 'ListTasksResponse', ], 'errors' => [ [ 'shape' => 'ServerException', ], [ 'shape' => 'ClientException', ], [ 'shape' => 'InvalidParameterException', ], [ 'shape' => 'ClusterNotFoundException', ], [ 'shape' => 'ServiceNotFoundException', ], ], ], 'RegisterContainerInstance' => [ 'name' => 'RegisterContainerInstance', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'RegisterContainerInstanceRequest', ], 'output' => [ 'shape' => 'RegisterContainerInstanceResponse', ], 'errors' => [ [ 'shape' => 'ServerException', ], [ 'shape' => 'ClientException', ], ], ], 'RegisterTaskDefinition' => [ 'name' => 'RegisterTaskDefinition', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'RegisterTaskDefinitionRequest', ], 'output' => [ 'shape' => 'RegisterTaskDefinitionResponse', ], 'errors' => [ [ 'shape' => 'ServerException', ], [ 'shape' => 'ClientException', ], [ 'shape' => 'InvalidParameterException', ], ], ], 'RunTask' => [ 'name' => 'RunTask', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'RunTaskRequest', ], 'output' => [ 'shape' => 'RunTaskResponse', ], 'errors' => [ [ 'shape' => 'ServerException', ], [ 'shape' => 'ClientException', ], [ 'shape' => 'InvalidParameterException', ], [ 'shape' => 'ClusterNotFoundException', ], ], ], 'StartTask' => [ 'name' => 'StartTask', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'StartTaskRequest', ], 'output' => [ 'shape' => 'StartTaskResponse', ], 'errors' => [ [ 'shape' => 'ServerException', ], [ 'shape' => 'ClientException', ], [ 'shape' => 'InvalidParameterException', ], [ 'shape' => 'ClusterNotFoundException', ], ], ], 'StopTask' => [ 'name' => 'StopTask', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'StopTaskRequest', ], 'output' => [ 'shape' => 'StopTaskResponse', ], 'errors' => [ [ 'shape' => 'ServerException', ], [ 'shape' => 'ClientException', ], [ 'shape' => 'InvalidParameterException', ], [ 'shape' => 'ClusterNotFoundException', ], ], ], 'SubmitContainerStateChange' => [ 'name' => 'SubmitContainerStateChange', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'SubmitContainerStateChangeRequest', ], 'output' => [ 'shape' => 'SubmitContainerStateChangeResponse', ], 'errors' => [ [ 'shape' => 'ServerException', ], [ 'shape' => 'ClientException', ], ], ], 'SubmitTaskStateChange' => [ 'name' => 'SubmitTaskStateChange', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'SubmitTaskStateChangeRequest', ], 'output' => [ 'shape' => 'SubmitTaskStateChangeResponse', ], 'errors' => [ [ 'shape' => 'ServerException', ], [ 'shape' => 'ClientException', ], ], ], 'UpdateContainerAgent' => [ 'name' => 'UpdateContainerAgent', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'UpdateContainerAgentRequest', ], 'output' => [ 'shape' => 'UpdateContainerAgentResponse', ], 'errors' => [ [ 'shape' => 'ServerException', ], [ 'shape' => 'ClientException', ], [ 'shape' => 'InvalidParameterException', ], [ 'shape' => 'ClusterNotFoundException', ], [ 'shape' => 'UpdateInProgressException', ], [ 'shape' => 'NoUpdateAvailableException', ], [ 'shape' => 'MissingVersionException', ], ], ], 'UpdateService' => [ 'name' => 'UpdateService', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'UpdateServiceRequest', ], 'output' => [ 'shape' => 'UpdateServiceResponse', ], 'errors' => [ [ 'shape' => 'ServerException', ], [ 'shape' => 'ClientException', ], [ 'shape' => 'InvalidParameterException', ], [ 'shape' => 'ClusterNotFoundException', ], [ 'shape' => 'ServiceNotFoundException', ], [ 'shape' => 'ServiceNotActiveException', ], ], ], ], 'shapes' => [ 'AgentUpdateStatus' => [ 'type' => 'string', 'enum' => [ 'PENDING', 'STAGING', 'STAGED', 'UPDATING', 'UPDATED', 'FAILED', ], ], 'Attribute' => [ 'type' => 'structure', 'required' => [ 'name', ], 'members' => [ 'name' => [ 'shape' => 'String', ], 'value' => [ 'shape' => 'String', ], ], ], 'Attributes' => [ 'type' => 'list', 'member' => [ 'shape' => 'Attribute', ], ], 'Boolean' => [ 'type' => 'boolean', ], 'BoxedBoolean' => [ 'type' => 'boolean', 'box' => true, ], 'BoxedInteger' => [ 'type' => 'integer', 'box' => true, ], 'ClientException' => [ 'type' => 'structure', 'members' => [ 'message' => [ 'shape' => 'String', ], ], 'exception' => true, ], 'Cluster' => [ 'type' => 'structure', 'members' => [ 'clusterArn' => [ 'shape' => 'String', ], 'clusterName' => [ 'shape' => 'String', ], 'status' => [ 'shape' => 'String', ], 'registeredContainerInstancesCount' => [ 'shape' => 'Integer', ], 'runningTasksCount' => [ 'shape' => 'Integer', ], 'pendingTasksCount' => [ 'shape' => 'Integer', ], 'activeServicesCount' => [ 'shape' => 'Integer', ], ], ], 'ClusterContainsContainerInstancesException' => [ 'type' => 'structure', 'members' => [], 'exception' => true, ], 'ClusterContainsServicesException' => [ 'type' => 'structure', 'members' => [], 'exception' => true, ], 'ClusterNotFoundException' => [ 'type' => 'structure', 'members' => [], 'exception' => true, ], 'Clusters' => [ 'type' => 'list', 'member' => [ 'shape' => 'Cluster', ], ], 'Container' => [ 'type' => 'structure', 'members' => [ 'containerArn' => [ 'shape' => 'String', ], 'taskArn' => [ 'shape' => 'String', ], 'name' => [ 'shape' => 'String', ], 'lastStatus' => [ 'shape' => 'String', ], 'exitCode' => [ 'shape' => 'BoxedInteger', ], 'reason' => [ 'shape' => 'String', ], 'networkBindings' => [ 'shape' => 'NetworkBindings', ], ], ], 'ContainerDefinition' => [ 'type' => 'structure', 'members' => [ 'name' => [ 'shape' => 'String', ], 'image' => [ 'shape' => 'String', ], 'cpu' => [ 'shape' => 'Integer', ], 'memory' => [ 'shape' => 'Integer', ], 'links' => [ 'shape' => 'StringList', ], 'portMappings' => [ 'shape' => 'PortMappingList', ], 'essential' => [ 'shape' => 'BoxedBoolean', ], 'entryPoint' => [ 'shape' => 'StringList', ], 'command' => [ 'shape' => 'StringList', ], 'environment' => [ 'shape' => 'EnvironmentVariables', ], 'mountPoints' => [ 'shape' => 'MountPointList', ], 'volumesFrom' => [ 'shape' => 'VolumeFromList', ], 'hostname' => [ 'shape' => 'String', ], 'user' => [ 'shape' => 'String', ], 'workingDirectory' => [ 'shape' => 'String', ], 'disableNetworking' => [ 'shape' => 'BoxedBoolean', ], 'privileged' => [ 'shape' => 'BoxedBoolean', ], 'readonlyRootFilesystem' => [ 'shape' => 'BoxedBoolean', ], 'dnsServers' => [ 'shape' => 'StringList', ], 'dnsSearchDomains' => [ 'shape' => 'StringList', ], 'extraHosts' => [ 'shape' => 'HostEntryList', ], 'dockerSecurityOptions' => [ 'shape' => 'StringList', ], 'dockerLabels' => [ 'shape' => 'DockerLabelsMap', ], 'ulimits' => [ 'shape' => 'UlimitList', ], 'logConfiguration' => [ 'shape' => 'LogConfiguration', ], ], ], 'ContainerDefinitions' => [ 'type' => 'list', 'member' => [ 'shape' => 'ContainerDefinition', ], ], 'ContainerInstance' => [ 'type' => 'structure', 'members' => [ 'containerInstanceArn' => [ 'shape' => 'String', ], 'ec2InstanceId' => [ 'shape' => 'String', ], 'versionInfo' => [ 'shape' => 'VersionInfo', ], 'remainingResources' => [ 'shape' => 'Resources', ], 'registeredResources' => [ 'shape' => 'Resources', ], 'status' => [ 'shape' => 'String', ], 'agentConnected' => [ 'shape' => 'Boolean', ], 'runningTasksCount' => [ 'shape' => 'Integer', ], 'pendingTasksCount' => [ 'shape' => 'Integer', ], 'agentUpdateStatus' => [ 'shape' => 'AgentUpdateStatus', ], 'attributes' => [ 'shape' => 'Attributes', ], ], ], 'ContainerInstances' => [ 'type' => 'list', 'member' => [ 'shape' => 'ContainerInstance', ], ], 'ContainerOverride' => [ 'type' => 'structure', 'members' => [ 'name' => [ 'shape' => 'String', ], 'command' => [ 'shape' => 'StringList', ], 'environment' => [ 'shape' => 'EnvironmentVariables', ], ], ], 'ContainerOverrides' => [ 'type' => 'list', 'member' => [ 'shape' => 'ContainerOverride', ], ], 'Containers' => [ 'type' => 'list', 'member' => [ 'shape' => 'Container', ], ], 'CreateClusterRequest' => [ 'type' => 'structure', 'members' => [ 'clusterName' => [ 'shape' => 'String', ], ], ], 'CreateClusterResponse' => [ 'type' => 'structure', 'members' => [ 'cluster' => [ 'shape' => 'Cluster', ], ], ], 'CreateServiceRequest' => [ 'type' => 'structure', 'required' => [ 'serviceName', 'taskDefinition', 'desiredCount', ], 'members' => [ 'cluster' => [ 'shape' => 'String', ], 'serviceName' => [ 'shape' => 'String', ], 'taskDefinition' => [ 'shape' => 'String', ], 'loadBalancers' => [ 'shape' => 'LoadBalancers', ], 'desiredCount' => [ 'shape' => 'BoxedInteger', ], 'clientToken' => [ 'shape' => 'String', ], 'role' => [ 'shape' => 'String', ], 'deploymentConfiguration' => [ 'shape' => 'DeploymentConfiguration', ], ], ], 'CreateServiceResponse' => [ 'type' => 'structure', 'members' => [ 'service' => [ 'shape' => 'Service', ], ], ], 'DeleteClusterRequest' => [ 'type' => 'structure', 'required' => [ 'cluster', ], 'members' => [ 'cluster' => [ 'shape' => 'String', ], ], ], 'DeleteClusterResponse' => [ 'type' => 'structure', 'members' => [ 'cluster' => [ 'shape' => 'Cluster', ], ], ], 'DeleteServiceRequest' => [ 'type' => 'structure', 'required' => [ 'service', ], 'members' => [ 'cluster' => [ 'shape' => 'String', ], 'service' => [ 'shape' => 'String', ], ], ], 'DeleteServiceResponse' => [ 'type' => 'structure', 'members' => [ 'service' => [ 'shape' => 'Service', ], ], ], 'Deployment' => [ 'type' => 'structure', 'members' => [ 'id' => [ 'shape' => 'String', ], 'status' => [ 'shape' => 'String', ], 'taskDefinition' => [ 'shape' => 'String', ], 'desiredCount' => [ 'shape' => 'Integer', ], 'pendingCount' => [ 'shape' => 'Integer', ], 'runningCount' => [ 'shape' => 'Integer', ], 'createdAt' => [ 'shape' => 'Timestamp', ], 'updatedAt' => [ 'shape' => 'Timestamp', ], ], ], 'DeploymentConfiguration' => [ 'type' => 'structure', 'members' => [ 'maximumPercent' => [ 'shape' => 'BoxedInteger', ], 'minimumHealthyPercent' => [ 'shape' => 'BoxedInteger', ], ], ], 'Deployments' => [ 'type' => 'list', 'member' => [ 'shape' => 'Deployment', ], ], 'DeregisterContainerInstanceRequest' => [ 'type' => 'structure', 'required' => [ 'containerInstance', ], 'members' => [ 'cluster' => [ 'shape' => 'String', ], 'containerInstance' => [ 'shape' => 'String', ], 'force' => [ 'shape' => 'BoxedBoolean', ], ], ], 'DeregisterContainerInstanceResponse' => [ 'type' => 'structure', 'members' => [ 'containerInstance' => [ 'shape' => 'ContainerInstance', ], ], ], 'DeregisterTaskDefinitionRequest' => [ 'type' => 'structure', 'required' => [ 'taskDefinition', ], 'members' => [ 'taskDefinition' => [ 'shape' => 'String', ], ], ], 'DeregisterTaskDefinitionResponse' => [ 'type' => 'structure', 'members' => [ 'taskDefinition' => [ 'shape' => 'TaskDefinition', ], ], ], 'DescribeClustersRequest' => [ 'type' => 'structure', 'members' => [ 'clusters' => [ 'shape' => 'StringList', ], ], ], 'DescribeClustersResponse' => [ 'type' => 'structure', 'members' => [ 'clusters' => [ 'shape' => 'Clusters', ], 'failures' => [ 'shape' => 'Failures', ], ], ], 'DescribeContainerInstancesRequest' => [ 'type' => 'structure', 'required' => [ 'containerInstances', ], 'members' => [ 'cluster' => [ 'shape' => 'String', ], 'containerInstances' => [ 'shape' => 'StringList', ], ], ], 'DescribeContainerInstancesResponse' => [ 'type' => 'structure', 'members' => [ 'containerInstances' => [ 'shape' => 'ContainerInstances', ], 'failures' => [ 'shape' => 'Failures', ], ], ], 'DescribeServicesRequest' => [ 'type' => 'structure', 'required' => [ 'services', ], 'members' => [ 'cluster' => [ 'shape' => 'String', ], 'services' => [ 'shape' => 'StringList', ], ], ], 'DescribeServicesResponse' => [ 'type' => 'structure', 'members' => [ 'services' => [ 'shape' => 'Services', ], 'failures' => [ 'shape' => 'Failures', ], ], ], 'DescribeTaskDefinitionRequest' => [ 'type' => 'structure', 'required' => [ 'taskDefinition', ], 'members' => [ 'taskDefinition' => [ 'shape' => 'String', ], ], ], 'DescribeTaskDefinitionResponse' => [ 'type' => 'structure', 'members' => [ 'taskDefinition' => [ 'shape' => 'TaskDefinition', ], ], ], 'DescribeTasksRequest' => [ 'type' => 'structure', 'required' => [ 'tasks', ], 'members' => [ 'cluster' => [ 'shape' => 'String', ], 'tasks' => [ 'shape' => 'StringList', ], ], ], 'DescribeTasksResponse' => [ 'type' => 'structure', 'members' => [ 'tasks' => [ 'shape' => 'Tasks', ], 'failures' => [ 'shape' => 'Failures', ], ], ], 'DesiredStatus' => [ 'type' => 'string', 'enum' => [ 'RUNNING', 'PENDING', 'STOPPED', ], ], 'DiscoverPollEndpointRequest' => [ 'type' => 'structure', 'members' => [ 'containerInstance' => [ 'shape' => 'String', ], 'cluster' => [ 'shape' => 'String', ], ], ], 'DiscoverPollEndpointResponse' => [ 'type' => 'structure', 'members' => [ 'endpoint' => [ 'shape' => 'String', ], 'telemetryEndpoint' => [ 'shape' => 'String', ], ], ], 'DockerLabelsMap' => [ 'type' => 'map', 'key' => [ 'shape' => 'String', ], 'value' => [ 'shape' => 'String', ], ], 'Double' => [ 'type' => 'double', ], 'EnvironmentVariables' => [ 'type' => 'list', 'member' => [ 'shape' => 'KeyValuePair', ], ], 'Failure' => [ 'type' => 'structure', 'members' => [ 'arn' => [ 'shape' => 'String', ], 'reason' => [ 'shape' => 'String', ], ], ], 'Failures' => [ 'type' => 'list', 'member' => [ 'shape' => 'Failure', ], ], 'HostEntry' => [ 'type' => 'structure', 'required' => [ 'hostname', 'ipAddress', ], 'members' => [ 'hostname' => [ 'shape' => 'String', ], 'ipAddress' => [ 'shape' => 'String', ], ], ], 'HostEntryList' => [ 'type' => 'list', 'member' => [ 'shape' => 'HostEntry', ], ], 'HostVolumeProperties' => [ 'type' => 'structure', 'members' => [ 'sourcePath' => [ 'shape' => 'String', ], ], ], 'Integer' => [ 'type' => 'integer', ], 'InvalidParameterException' => [ 'type' => 'structure', 'members' => [], 'exception' => true, ], 'KeyValuePair' => [ 'type' => 'structure', 'members' => [ 'name' => [ 'shape' => 'String', ], 'value' => [ 'shape' => 'String', ], ], ], 'ListClustersRequest' => [ 'type' => 'structure', 'members' => [ 'nextToken' => [ 'shape' => 'String', ], 'maxResults' => [ 'shape' => 'BoxedInteger', ], ], ], 'ListClustersResponse' => [ 'type' => 'structure', 'members' => [ 'clusterArns' => [ 'shape' => 'StringList', ], 'nextToken' => [ 'shape' => 'String', ], ], ], 'ListContainerInstancesRequest' => [ 'type' => 'structure', 'members' => [ 'cluster' => [ 'shape' => 'String', ], 'nextToken' => [ 'shape' => 'String', ], 'maxResults' => [ 'shape' => 'BoxedInteger', ], ], ], 'ListContainerInstancesResponse' => [ 'type' => 'structure', 'members' => [ 'containerInstanceArns' => [ 'shape' => 'StringList', ], 'nextToken' => [ 'shape' => 'String', ], ], ], 'ListServicesRequest' => [ 'type' => 'structure', 'members' => [ 'cluster' => [ 'shape' => 'String', ], 'nextToken' => [ 'shape' => 'String', ], 'maxResults' => [ 'shape' => 'BoxedInteger', ], ], ], 'ListServicesResponse' => [ 'type' => 'structure', 'members' => [ 'serviceArns' => [ 'shape' => 'StringList', ], 'nextToken' => [ 'shape' => 'String', ], ], ], 'ListTaskDefinitionFamiliesRequest' => [ 'type' => 'structure', 'members' => [ 'familyPrefix' => [ 'shape' => 'String', ], 'status' => [ 'shape' => 'TaskDefinitionFamilyStatus', ], 'nextToken' => [ 'shape' => 'String', ], 'maxResults' => [ 'shape' => 'BoxedInteger', ], ], ], 'ListTaskDefinitionFamiliesResponse' => [ 'type' => 'structure', 'members' => [ 'families' => [ 'shape' => 'StringList', ], 'nextToken' => [ 'shape' => 'String', ], ], ], 'ListTaskDefinitionsRequest' => [ 'type' => 'structure', 'members' => [ 'familyPrefix' => [ 'shape' => 'String', ], 'status' => [ 'shape' => 'TaskDefinitionStatus', ], 'sort' => [ 'shape' => 'SortOrder', ], 'nextToken' => [ 'shape' => 'String', ], 'maxResults' => [ 'shape' => 'BoxedInteger', ], ], ], 'ListTaskDefinitionsResponse' => [ 'type' => 'structure', 'members' => [ 'taskDefinitionArns' => [ 'shape' => 'StringList', ], 'nextToken' => [ 'shape' => 'String', ], ], ], 'ListTasksRequest' => [ 'type' => 'structure', 'members' => [ 'cluster' => [ 'shape' => 'String', ], 'containerInstance' => [ 'shape' => 'String', ], 'family' => [ 'shape' => 'String', ], 'nextToken' => [ 'shape' => 'String', ], 'maxResults' => [ 'shape' => 'BoxedInteger', ], 'startedBy' => [ 'shape' => 'String', ], 'serviceName' => [ 'shape' => 'String', ], 'desiredStatus' => [ 'shape' => 'DesiredStatus', ], ], ], 'ListTasksResponse' => [ 'type' => 'structure', 'members' => [ 'taskArns' => [ 'shape' => 'StringList', ], 'nextToken' => [ 'shape' => 'String', ], ], ], 'LoadBalancer' => [ 'type' => 'structure', 'members' => [ 'loadBalancerName' => [ 'shape' => 'String', ], 'containerName' => [ 'shape' => 'String', ], 'containerPort' => [ 'shape' => 'BoxedInteger', ], ], ], 'LoadBalancers' => [ 'type' => 'list', 'member' => [ 'shape' => 'LoadBalancer', ], ], 'LogConfiguration' => [ 'type' => 'structure', 'required' => [ 'logDriver', ], 'members' => [ 'logDriver' => [ 'shape' => 'LogDriver', ], 'options' => [ 'shape' => 'LogConfigurationOptionsMap', ], ], ], 'LogConfigurationOptionsMap' => [ 'type' => 'map', 'key' => [ 'shape' => 'String', ], 'value' => [ 'shape' => 'String', ], ], 'LogDriver' => [ 'type' => 'string', 'enum' => [ 'json-file', 'syslog', 'journald', 'gelf', 'fluentd', 'awslogs', ], ], 'Long' => [ 'type' => 'long', ], 'MissingVersionException' => [ 'type' => 'structure', 'members' => [], 'exception' => true, ], 'MountPoint' => [ 'type' => 'structure', 'members' => [ 'sourceVolume' => [ 'shape' => 'String', ], 'containerPath' => [ 'shape' => 'String', ], 'readOnly' => [ 'shape' => 'BoxedBoolean', ], ], ], 'MountPointList' => [ 'type' => 'list', 'member' => [ 'shape' => 'MountPoint', ], ], 'NetworkBinding' => [ 'type' => 'structure', 'members' => [ 'bindIP' => [ 'shape' => 'String', ], 'containerPort' => [ 'shape' => 'BoxedInteger', ], 'hostPort' => [ 'shape' => 'BoxedInteger', ], 'protocol' => [ 'shape' => 'TransportProtocol', ], ], ], 'NetworkBindings' => [ 'type' => 'list', 'member' => [ 'shape' => 'NetworkBinding', ], ], 'NoUpdateAvailableException' => [ 'type' => 'structure', 'members' => [], 'exception' => true, ], 'PortMapping' => [ 'type' => 'structure', 'members' => [ 'containerPort' => [ 'shape' => 'Integer', ], 'hostPort' => [ 'shape' => 'Integer', ], 'protocol' => [ 'shape' => 'TransportProtocol', ], ], ], 'PortMappingList' => [ 'type' => 'list', 'member' => [ 'shape' => 'PortMapping', ], ], 'RegisterContainerInstanceRequest' => [ 'type' => 'structure', 'members' => [ 'cluster' => [ 'shape' => 'String', ], 'instanceIdentityDocument' => [ 'shape' => 'String', ], 'instanceIdentityDocumentSignature' => [ 'shape' => 'String', ], 'totalResources' => [ 'shape' => 'Resources', ], 'versionInfo' => [ 'shape' => 'VersionInfo', ], 'containerInstanceArn' => [ 'shape' => 'String', ], 'attributes' => [ 'shape' => 'Attributes', ], ], ], 'RegisterContainerInstanceResponse' => [ 'type' => 'structure', 'members' => [ 'containerInstance' => [ 'shape' => 'ContainerInstance', ], ], ], 'RegisterTaskDefinitionRequest' => [ 'type' => 'structure', 'required' => [ 'family', 'containerDefinitions', ], 'members' => [ 'family' => [ 'shape' => 'String', ], 'containerDefinitions' => [ 'shape' => 'ContainerDefinitions', ], 'volumes' => [ 'shape' => 'VolumeList', ], ], ], 'RegisterTaskDefinitionResponse' => [ 'type' => 'structure', 'members' => [ 'taskDefinition' => [ 'shape' => 'TaskDefinition', ], ], ], 'RequiresAttributes' => [ 'type' => 'list', 'member' => [ 'shape' => 'Attribute', ], ], 'Resource' => [ 'type' => 'structure', 'members' => [ 'name' => [ 'shape' => 'String', ], 'type' => [ 'shape' => 'String', ], 'doubleValue' => [ 'shape' => 'Double', ], 'longValue' => [ 'shape' => 'Long', ], 'integerValue' => [ 'shape' => 'Integer', ], 'stringSetValue' => [ 'shape' => 'StringList', ], ], ], 'Resources' => [ 'type' => 'list', 'member' => [ 'shape' => 'Resource', ], ], 'RunTaskRequest' => [ 'type' => 'structure', 'required' => [ 'taskDefinition', ], 'members' => [ 'cluster' => [ 'shape' => 'String', ], 'taskDefinition' => [ 'shape' => 'String', ], 'overrides' => [ 'shape' => 'TaskOverride', ], 'count' => [ 'shape' => 'BoxedInteger', ], 'startedBy' => [ 'shape' => 'String', ], ], ], 'RunTaskResponse' => [ 'type' => 'structure', 'members' => [ 'tasks' => [ 'shape' => 'Tasks', ], 'failures' => [ 'shape' => 'Failures', ], ], ], 'ServerException' => [ 'type' => 'structure', 'members' => [ 'message' => [ 'shape' => 'String', ], ], 'exception' => true, 'fault' => true, ], 'Service' => [ 'type' => 'structure', 'members' => [ 'serviceArn' => [ 'shape' => 'String', ], 'serviceName' => [ 'shape' => 'String', ], 'clusterArn' => [ 'shape' => 'String', ], 'loadBalancers' => [ 'shape' => 'LoadBalancers', ], 'status' => [ 'shape' => 'String', ], 'desiredCount' => [ 'shape' => 'Integer', ], 'runningCount' => [ 'shape' => 'Integer', ], 'pendingCount' => [ 'shape' => 'Integer', ], 'taskDefinition' => [ 'shape' => 'String', ], 'deploymentConfiguration' => [ 'shape' => 'DeploymentConfiguration', ], 'deployments' => [ 'shape' => 'Deployments', ], 'roleArn' => [ 'shape' => 'String', ], 'events' => [ 'shape' => 'ServiceEvents', ], 'createdAt' => [ 'shape' => 'Timestamp', ], ], ], 'ServiceEvent' => [ 'type' => 'structure', 'members' => [ 'id' => [ 'shape' => 'String', ], 'createdAt' => [ 'shape' => 'Timestamp', ], 'message' => [ 'shape' => 'String', ], ], ], 'ServiceEvents' => [ 'type' => 'list', 'member' => [ 'shape' => 'ServiceEvent', ], ], 'ServiceNotActiveException' => [ 'type' => 'structure', 'members' => [], 'exception' => true, ], 'ServiceNotFoundException' => [ 'type' => 'structure', 'members' => [], 'exception' => true, ], 'Services' => [ 'type' => 'list', 'member' => [ 'shape' => 'Service', ], ], 'SortOrder' => [ 'type' => 'string', 'enum' => [ 'ASC', 'DESC', ], ], 'StartTaskRequest' => [ 'type' => 'structure', 'required' => [ 'taskDefinition', 'containerInstances', ], 'members' => [ 'cluster' => [ 'shape' => 'String', ], 'taskDefinition' => [ 'shape' => 'String', ], 'overrides' => [ 'shape' => 'TaskOverride', ], 'containerInstances' => [ 'shape' => 'StringList', ], 'startedBy' => [ 'shape' => 'String', ], ], ], 'StartTaskResponse' => [ 'type' => 'structure', 'members' => [ 'tasks' => [ 'shape' => 'Tasks', ], 'failures' => [ 'shape' => 'Failures', ], ], ], 'StopTaskRequest' => [ 'type' => 'structure', 'required' => [ 'task', ], 'members' => [ 'cluster' => [ 'shape' => 'String', ], 'task' => [ 'shape' => 'String', ], 'reason' => [ 'shape' => 'String', ], ], ], 'StopTaskResponse' => [ 'type' => 'structure', 'members' => [ 'task' => [ 'shape' => 'Task', ], ], ], 'String' => [ 'type' => 'string', ], 'StringList' => [ 'type' => 'list', 'member' => [ 'shape' => 'String', ], ], 'SubmitContainerStateChangeRequest' => [ 'type' => 'structure', 'members' => [ 'cluster' => [ 'shape' => 'String', ], 'task' => [ 'shape' => 'String', ], 'containerName' => [ 'shape' => 'String', ], 'status' => [ 'shape' => 'String', ], 'exitCode' => [ 'shape' => 'BoxedInteger', ], 'reason' => [ 'shape' => 'String', ], 'networkBindings' => [ 'shape' => 'NetworkBindings', ], ], ], 'SubmitContainerStateChangeResponse' => [ 'type' => 'structure', 'members' => [ 'acknowledgment' => [ 'shape' => 'String', ], ], ], 'SubmitTaskStateChangeRequest' => [ 'type' => 'structure', 'members' => [ 'cluster' => [ 'shape' => 'String', ], 'task' => [ 'shape' => 'String', ], 'status' => [ 'shape' => 'String', ], 'reason' => [ 'shape' => 'String', ], ], ], 'SubmitTaskStateChangeResponse' => [ 'type' => 'structure', 'members' => [ 'acknowledgment' => [ 'shape' => 'String', ], ], ], 'Task' => [ 'type' => 'structure', 'members' => [ 'taskArn' => [ 'shape' => 'String', ], 'clusterArn' => [ 'shape' => 'String', ], 'taskDefinitionArn' => [ 'shape' => 'String', ], 'containerInstanceArn' => [ 'shape' => 'String', ], 'overrides' => [ 'shape' => 'TaskOverride', ], 'lastStatus' => [ 'shape' => 'String', ], 'desiredStatus' => [ 'shape' => 'String', ], 'containers' => [ 'shape' => 'Containers', ], 'startedBy' => [ 'shape' => 'String', ], 'stoppedReason' => [ 'shape' => 'String', ], 'createdAt' => [ 'shape' => 'Timestamp', ], 'startedAt' => [ 'shape' => 'Timestamp', ], 'stoppedAt' => [ 'shape' => 'Timestamp', ], ], ], 'TaskDefinition' => [ 'type' => 'structure', 'members' => [ 'taskDefinitionArn' => [ 'shape' => 'String', ], 'containerDefinitions' => [ 'shape' => 'ContainerDefinitions', ], 'family' => [ 'shape' => 'String', ], 'revision' => [ 'shape' => 'Integer', ], 'volumes' => [ 'shape' => 'VolumeList', ], 'status' => [ 'shape' => 'TaskDefinitionStatus', ], 'requiresAttributes' => [ 'shape' => 'RequiresAttributes', ], ], ], 'TaskDefinitionFamilyStatus' => [ 'type' => 'string', 'enum' => [ 'ACTIVE', 'INACTIVE', 'ALL', ], ], 'TaskDefinitionStatus' => [ 'type' => 'string', 'enum' => [ 'ACTIVE', 'INACTIVE', ], ], 'TaskOverride' => [ 'type' => 'structure', 'members' => [ 'containerOverrides' => [ 'shape' => 'ContainerOverrides', ], ], ], 'Tasks' => [ 'type' => 'list', 'member' => [ 'shape' => 'Task', ], ], 'Timestamp' => [ 'type' => 'timestamp', ], 'TransportProtocol' => [ 'type' => 'string', 'enum' => [ 'tcp', 'udp', ], ], 'Ulimit' => [ 'type' => 'structure', 'required' => [ 'name', 'softLimit', 'hardLimit', ], 'members' => [ 'name' => [ 'shape' => 'UlimitName', ], 'softLimit' => [ 'shape' => 'Integer', ], 'hardLimit' => [ 'shape' => 'Integer', ], ], ], 'UlimitList' => [ 'type' => 'list', 'member' => [ 'shape' => 'Ulimit', ], ], 'UlimitName' => [ 'type' => 'string', 'enum' => [ 'core', 'cpu', 'data', 'fsize', 'locks', 'memlock', 'msgqueue', 'nice', 'nofile', 'nproc', 'rss', 'rtprio', 'rttime', 'sigpending', 'stack', ], ], 'UpdateContainerAgentRequest' => [ 'type' => 'structure', 'required' => [ 'containerInstance', ], 'members' => [ 'cluster' => [ 'shape' => 'String', ], 'containerInstance' => [ 'shape' => 'String', ], ], ], 'UpdateContainerAgentResponse' => [ 'type' => 'structure', 'members' => [ 'containerInstance' => [ 'shape' => 'ContainerInstance', ], ], ], 'UpdateInProgressException' => [ 'type' => 'structure', 'members' => [], 'exception' => true, ], 'UpdateServiceRequest' => [ 'type' => 'structure', 'required' => [ 'service', ], 'members' => [ 'cluster' => [ 'shape' => 'String', ], 'service' => [ 'shape' => 'String', ], 'desiredCount' => [ 'shape' => 'BoxedInteger', ], 'taskDefinition' => [ 'shape' => 'String', ], 'deploymentConfiguration' => [ 'shape' => 'DeploymentConfiguration', ], ], ], 'UpdateServiceResponse' => [ 'type' => 'structure', 'members' => [ 'service' => [ 'shape' => 'Service', ], ], ], 'VersionInfo' => [ 'type' => 'structure', 'members' => [ 'agentVersion' => [ 'shape' => 'String', ], 'agentHash' => [ 'shape' => 'String', ], 'dockerVersion' => [ 'shape' => 'String', ], ], ], 'Volume' => [ 'type' => 'structure', 'members' => [ 'name' => [ 'shape' => 'String', ], 'host' => [ 'shape' => 'HostVolumeProperties', ], ], ], 'VolumeFrom' => [ 'type' => 'structure', 'members' => [ 'sourceContainer' => [ 'shape' => 'String', ], 'readOnly' => [ 'shape' => 'BoxedBoolean', ], ], ], 'VolumeFromList' => [ 'type' => 'list', 'member' => [ 'shape' => 'VolumeFrom', ], ], 'VolumeList' => [ 'type' => 'list', 'member' => [ 'shape' => 'Volume', ], ], ],];
