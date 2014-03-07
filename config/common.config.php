<?php
/**
 * This file is part of the DreamFactory Services Platform(tm) (DSP)
 *
 * DreamFactory Services Platform(tm) <http://github.com/dreamfactorysoftware/dsp-core>
 * Copyright 2012-2013 DreamFactory Software, Inc. <developer-support@dreamfactory.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
use DreamFactory\Platform\Utility\Fabric;

/**
 * common.config.php
 * This file contains any application-level parameters that are to be shared between the background and web services
 */
if ( !defined( 'DSP_VERSION' ) )
{
	require __DIR__ . '/constants.config.php';
}

//*************************************************************************
//* Global Configuration Settings
//*************************************************************************

//	The base path of the project, where it's checked out basically
$_basePath = dirname( __DIR__ );
//	The document root
$_docRoot = $_basePath . '/web';
//	The vendor path
$_vendorPath = $_basePath . '/vendor';
//	Set to false to disable database caching
$_dbCacheEnabled = true;
//	The name of the default controller. "site" just sucks
$_defaultController = 'web';
//	Where the log files go and the name...
$_logFilePath = $_basePath . '/log';
$_logFileName = 'web.' . ( isset( $_SERVER, $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : 'unknown' ) . '.log';
$_appName = 'DreamFactory Services Platform';

/**
 * Aliases
 */
/** @noinspection PhpIncludeInspection */
file_exists( __DIR__ . ALIASES_CONFIG_PATH ) && require __DIR__ . ALIASES_CONFIG_PATH;

/**
 * Salts
 */
$_dspSalts = array();

/** @noinspection PhpIncludeInspection */
if ( file_exists( __DIR__ . SALT_CONFIG_PATH ) && $_salts = require( __DIR__ . SALT_CONFIG_PATH ) )
{
	if ( !empty( $_salts ) )
	{
		foreach ( $_salts as $_key => $_salt )
		{
			if ( $_salt )
			{
				$_dspSalts['dsp.salts.' . $_key] = $_salt;
			}
		}
	}

	unset( $_salts );
}

/**
 * Application Paths
 */
\Kisma::set(
	array(
		'app.app_name'      => $_appName,
		'app.doc_root'      => $_docRoot,
		'app.log_path'      => $_logFilePath,
		'app.vendor_path'   => $_vendorPath,
		'app.log_file_name' => $_logFileName,
		'app.project_root'  => $_basePath,
	)
);

/**
 * Database Caching
 */
$_dbCache = $_dbCacheEnabled ? array(
	'class'                => 'CDbCache',
	'connectionID'         => 'db',
	'cacheTableName'       => 'df_sys_cache',
	'autoCreateCacheTable' => true,
) : null;

/**
 * Set up and return the common settings...
 */
if ( Fabric::fabricHosted() )
{
	$_storageBasePath = '/data/storage/' . \Kisma::get( 'platform.storage_key' );
	$_privatePath = \Kisma::get( 'platform.private_path' );
	$_storagePath = $_storageBasePath . '/blob';

	$_instanceSettings = array(
		'private_path'           => $_privatePath,
		'local_config_path'      => $_privatePath . '/config',
		'snapshot_path'          => $_privatePath . '/snapshots',
		'storage_base_path'      => $_storageBasePath,
		'storage_path'           => $_storagePath,
		'applications_path'      => $_storagePath . '/applications',
		'library_path'           => $_storagePath . '/plugins',
		'plugins_path'           => $_storagePath . '/plugins',
		'swagger_path'           => $_storagePath . '/swagger',
		'dsp_name'               => \Kisma::get( 'platform.dsp_name' ),
		'dsp.storage_id'         => \Kisma::get( 'platform.storage_key' ),
		'dsp.private_storage_id' => \Kisma::get( 'platform.private_storage_key' ),
	);

	unset( $_storageBasePath, $_privatePath, $_storagePath );
}
else
{
	$_storagePath = $_basePath . '/storage';
	$_privatePath = $_basePath . '/storage/.private';

	$_instanceSettings = array(
		'private_path'           => $_privatePath,
		'local_config_path'      => $_privatePath . '/config',
		'snapshot_path'          => $_privatePath . '/snapshots',
		'storage_base_path'      => $_storagePath,
		'storage_path'           => $_storagePath,
		'applications_path'      => $_storagePath . '/applications',
		'library_path'           => $_storagePath . '/plugins',
		'plugins_path'           => $_storagePath . '/plugins',
		'swagger_path'           => $_storagePath . '/swagger',
		'dsp_name'               => gethostname(),
		'dsp.storage_id'         => null,
		'dsp.private_storage_id' => null,
	);
}

/** @noinspection PhpIncludeInspection */
return array_merge(
	$_instanceSettings,
	array(
		/**
		 * App Information
		 */
		'base_path'                     => $_basePath,
		/**
		 * DSP Information
		 */
		'dsp.version'                   => DSP_VERSION,
		'dsp.name'                      => $_instanceSettings['dsp_name'],
		'dsp.auth_endpoint'             => DEFAULT_INSTANCE_AUTH_ENDPOINT,
		'dsp.enable_profiler'           => false,
		'dsp.log_events'                => false,
		'cloud.endpoint'                => DEFAULT_CLOUD_API_ENDPOINT,
		'oauth.salt'                    => 'rW64wRUk6Ocs+5c7JwQ{69U{]MBdIHqmx9Wj,=C%S#cA%+?!cJMbaQ+juMjHeEx[dlSe%h%kcI',
		/**
		 * Remote Logins
		 */
		'dsp.allow_remote_logins'       => true,
		'dsp.allow_admin_remote_logins' => true,
		/**
		 * User data
		 */
		'adminEmail'                    => DEFAULT_SUPPORT_EMAIL,
		/**
		 * The default service configuration
		 */
		'dsp.service_config'            => require( __DIR__ . SERVICES_CONFIG_PATH ),
		/** Array of namespaces to locations for service discovery */
		'dsp.service_location_map'      => array(),
		/**
		 * Default services provided by all DSPs
		 */
		'dsp.default_services'          => array(
			array( 'api_name' => 'user', 'name' => 'User Login' ),
			array( 'api_name' => 'system', 'name' => 'System Configuration' ),
			array( 'api_name' => 'api_docs', 'name' => 'API Documentation' ),
		),
		/**
		 * The default application to start
		 */
		'dsp.default_app'               => '/launchpad/index.html',
		/**
		 * The default landing pages for email confirmations
		 */
		'dsp.confirm_invite_url'        => '/web/confirmInvite',
		'dsp.confirm_register_url'      => '/web/confirmRegister',
		'dsp.confirm_reset_url'         => '/web/confirmPassword',
		/**
		 * The default number of records to return at once for database queries
		 */
		'dsp.db_max_records_returned'   => 1000,
		/**
		 * The default admin resource schema
		 */
		'admin.resource_schema'         => require( __DIR__ . DEFAULT_ADMIN_RESOURCE_SCHEMA ),
		'admin.default_theme'           => 'united',
	),
	$_dspSalts
);
