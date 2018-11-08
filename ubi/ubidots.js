var Request = {
  execute : function( settings ) {
    jQuery.ajax( settings );
  }
};

var RestClient = {
  get : function( url, headers, callback ) {
    Request.execute( {
      url : url,
      headers : headers,
      method : 'GET',
      complete : callback
    } );
  },
  post : function( url, payload, headers, callback ) {
    Request.execute( {
      url : url,
      data : payload,
      headers : headers,
      method : 'POST',
      complete : callback
    } );
  },
  delete : function( url, headers, callback ) {
    Request.execute( {
      url : url,
      headers : headers,
      method : 'DELETE',
      complete : callback
    } );
  }
};

var Ubidots = {
  VERSION : '0.0.1',
  Constants : {
    API_URL : 'http://things.ubidots.com/api/v1.6/'
  },
  ApiClient : function( apiKey, token, baseUrl, bridgeParam ) {
    var bridge = bridgeParam;
    
    if ( !( bridge instanceof Ubidots.ServerBridge ) ) {
      bridge = new Ubidots.ServerBridge( apiKey, token, baseUrl );
    }
    
    this.getDatasources = function( callback ) {
      bridge.get( 'datasources', function( response ) {
        var rawItems = response[ 'results' ];
        
        callback( bridge.transformToDatasourceObjects( rawItems ) );
      } );
    };
    
    this.getVariables = function( callback ) {
      bridge.get( 'variables', function( response ) {
        var rawItems = response[ 'results' ];
        
        callback( bridge.transformToVariableObjects( rawItems ) );
      } );
    };
    
    this.getDatasource = function( id, callback ) {
      var endpoint = 'datasources/' + id;
      
      bridge.get( endpoint, function( response ) {
        callback( new Ubidots.Datasource( bridge, response ) );
      } );
    };
    
    this.createDatasource = function( data, callback ) {
      var endpoint = 'datasources';
      
      bridge.post( endpoint, data, function( response ) {
        callback( new Ubidots.Datasource( bridge, response ) );
      } );
    };
    
    this.getVariable = function( id, callback ) {
      var endpoint = 'variables/' + id;
      
      bridge.get( endpoint, function( response ) {
        callback( new Ubidots.Variable( bridge, response ) );
      } );
    };
  },
  Datasource : function( bridgeParam, data ) {
    var self = this;
    var bridge = bridgeParam;
    this.id = data[ 'id' ];
    this.name = data[ 'name' ];
    this.url = data[ 'url' ];
    this.lastActivity = data[ 'last_activity' ];
    this.tags = data[ 'tags' ];
    this.description = data[ 'description' ];
    this.createdAt = data[ 'created_at' ];
    this.owner = data[ 'owner' ];
    this.parent = data[ 'parent' ];
    this.context = data[ 'context' ];
    this.variablesUrl = data[ 'variables_url' ];
    this.numberOfVariables = data[ 'number_of_variables' ];
    
    this.getVariables = function( callback ) {
      var endpoint = 'datasources/' + self.id + '/variables';
      
      bridge.get( endpoint, function( response ) {
        var rawItems = response[ 'results' ];
        
        callback( bridge.transformToVariableObjects( rawItems ) );
      } );
    };
    
    this.removeDatasource = function() {
      var endpoint = 'datasources/' + self.id;
      
      bridge.delete( endpoint );
    };
    
    this.createVariable = function( data, callback ) {
      var endpoint = 'datasources/' + self.id + '/variables';
      
      bridge.post( endpoint, data, function( response ) {
        callback( new Ubidots.Variable( bridge, response ) );
      } );
    };
  },
  ServerBridge : function( apiKeyParam, tokenParam, baseUrlParam ) {
    var self = this;
    var apiKey = undefined;
    var token = undefined;
    var baseUrl = ( typeof( baseUrlParam ) === 'string' ) ? baseUrlParam : Ubidots.Constants.API_URL;
    var apiKeyHeader = {};
    var tokenHeader = {};
    
    var getToken = function( callback ) {
      var endpoint = 'auth/token/';
      
      self.postWithApiKey( endpoint, function( response ) {
        token = response[ 'token' ];
        if ( typeof( callback ) === 'function' ) { callback( token ); }
      } );
    };
    
    var setApiKeyHeader = function() {
      apiKeyHeader = {
        'X-UBIDOTS-APIKEY' : apiKey
      };
    };
    
    var setTokenHeader = function() {
      tokenHeader = {
        'X-AUTH-TOKEN' : token
      };
    };
    
    var prepareData = function( data ) {
      return( data );
    };
    
    this.transformToDatasourceObjects = function( rawItems ) {
      var datasources = [];
      
      jQuery.each( rawItems, function( i, rawItem ) {
        datasources[ i ] = new Ubidots.Datasource( self, rawItem );
      } );
      
      return( datasources );
    };
    
    this.transformToVariableObjects = function( rawItems ) {
      var variables = [];
      
      jQuery.each( rawItems, function( i, rawItem ) {
        variables[ i ] = new Ubidots.Variable( self, rawItem );
      } );
      
      return( variables );
    };
    
    this.postWithApiKey = function( endpoint, callback ) {
      var headers = apiKeyHeader;
      
      RestClient.post( ( baseUrl + endpoint ), {}, headers, function( jqXHR, textStatus ) {
        if ( textStatus === 'success' ) {
          callback( jqXHR.responseJSON );
        }
      } );
    };
    
    this.get = function( endpoint, callback ) {
      var headers = tokenHeader;
      
      RestClient.get( ( baseUrl + endpoint ), headers, function( jqXHR, textStatus ) {
        if ( textStatus === 'success' ) {
          callback( jqXHR.responseJSON );
        }
      } );
    };
    
    this.getWithUrl = function( url, callback ) {
      var headers = tokenHeader;
      
      RestClient.get( url, headers, function( jqXHR, textStatus ) {
        if ( textStatus === 'success' ) {
          callback( jqXHR.responseJSON );
        }
      } );
    };
    
    this.post = function( endpoint, data, callback ) {
      var headers = tokenHeader;
      var data = prepareData( data );
      
      RestClient.post( ( baseUrl + endpoint ), data, headers, function( jqXHR, textStatus ) {
        if ( textStatus === 'success' ) {
          callback( jqXHR.responseJSON );
        }
      } );
    };
    
    this.delete = function( endpoint ) {
      var headers = tokenHeader;
      
      RestClient.delete( ( baseUrl + endpoint ), headers );
    };
    
    if ( typeof( apiKeyParam ) === 'string' ) {
      apiKey = apiKeyParam;
      setApiKeyHeader();
      getToken( setTokenHeader );
    } else if ( typeof( token ) === 'string' ) {
      token = tokenParam;
      setTokenHeader();
    }
  },
  Variable : function( bridgeParam, data ) {
    var self = this;
    var bridge = bridgeParam;
    var datasource = undefined;
    this.id = data[ 'id' ];
    this.name = data[ 'name' ];
    this.url = data[ 'url' ];
    this.lastActivity = data[ 'last_activity' ];
    this.tags = data[ 'tags' ];
    this.description = data[ 'description' ];
    this.createdAt = data[ 'created_at' ];
    this.icon = data[ 'icon' ];
    this.unit = data[ 'unit' ];
    this.rawDatasource = data[ 'datasource' ];
    this.properties = data[ 'properties' ];
    this.valuesUrl = data[ 'values_url' ];
    this.lastValue = data[ 'last_value' ];
    
    this.getValues = function( callback ) {
      var endpoint = 'variables/' + self.id + '/values';
      
      bridge.get( endpoint, function( response ) {
        callback( response[ 'results' ] );
      } );
    };
    
    this.saveValue = function( data, callback ) {
      var endpoint = 'variables/' + self.id + '/values';
      
      bridge.post( endpoint, data, callback );
    };
    
    this.saveValues = function( data, force, callback ) {
      var endpoint = 'variables/' + self.id + '/values';
      
      if ( force === true ) {
        endpoint = endpoint + '?force=true';
      }
      
      bridge.post( endpoint, data, callback );
    };
    
    this.removeVariable = function() {
      var endpoint = 'variables/' + self.id;
      
      bridge.delete( endpoint );
    };
    
    this.getDatasource = function( callback ) {
      if ( typeof( datasource ) === 'undefined' ) {
        var datasourceId = self.rawDatasource[ 'id' ];
        var endpoint = 'datasources/' + datasourceId;
        
        bridge.get( endpoint, function( response ) {
          callback( new Ubidots.Datasource( bridge, response ) );
        } );
      } else {
        callback( datasource );
      }
    };
  }
};
