# Ubidots JavaScript API Client

The Ubidots JavaScript API Client makes calls to the [Ubidots Api](http://things.ubidots.com/api/).

Installation
------------

Add `jQuery` and the `ubidots.js` file to your proyect:

```html5
<script src="//code.jquery.com/jquery.min.js"></script>
<script src="ubidots.js"></script>
```

Connecting to the API
----------------------

Before playing with the API you should connect to it using your private API key, which can be found [in your profile](http://app.ubidots.com/userdata/api/).

If you don't have an account yet, you can [create one here](http://app.ubidots.com/accounts/signup/).

Once you have your API key, you can connect to the API by creating an ApiClient instance. Let's assume your API key is: `"7fj39fk3044045k89fbh34rsd9823jkfs8323"`. Then your code would look like this:

```javascript
var api = new Ubidots.ApiClient( '7fj39fk3044045k89fbh34rsd9823jkfs8323' );
```

Now you have an instance of ApiClient ("api") which can be used to connect to the Ubidots API.

Saving a New Value to a Variable
--------------------------------

Retrieve the variable you'd like the value to be saved to:

```javascript
var myVariable;
api.getVariable( '56799cf1231b28459f976417', function( variable ) { myVariable = variable; } );
```

Given the instantiated variable, you can save a new value with the following line:

```javascript
var newValue;
myVariable.saveValue( { value : 10 }, function( value ) { newValue = value; } );
```

You can also specify a timestamp (optional):

```javascript
var newValue;
myVariable.saveValue( { value : 10, timestamp : 1376061804407 }, function( value ) { newValue = value; } );
```

If no timestamp is specified, the API server will assign the current time to it. We think it's always better for you to specify the timestamp so the record reflects the exact time the value was captured, not the time it arrived to our servers.

Creating a Data Source
----------------------

As you might know by now, a data source represents a device that's generating time-series data.

This line creates a new data source:

```javascript
var newDatasource;
api.createDatasource( { name : 'myNewDs', tags : [ 'firstDs', 'new' ], description : 'any des' }, function( datasource ) { newDatasource = datasource; } );
```

The `name` key is required, but the `tags` and `description` keys are optional. This new data source can be used to track different variables, so let's create one.

Creating a Variable
--------------------

A variable is a time-series containing different values over time. Let's create one:

```javascript
var newVariable;
newDatasource.createVariable( { name : 'myNewVar', unit : 'Nw' }, function( variable ) { newVariable = variable; } );
```

The `name` and `unit` keys are required.

Getting Values
--------------

To get the values of a variable, use the method `getValues` in an instance of the class `Variable`. This will return a values array.

If you only want the last N values call the method with the number of elements you want.

```javascript
var allValues;
myVariable.getValues( function( values ) { allValues = values; } );
```

Getting a Group of Data Sources
--------------------------------

If you want to get all your data sources you can call the `getDatasources` method on the ApiClient instance directly. This method return a objects Datasource array.

```javascript
var allDatasources;
api.getDatasources( function( datasources ) { allDatasources = datasources; } );
```

Getting a Specific Data source
-------------------------------

Each data source is identified by an ID. A specific data source can be retrieved from the server using this ID.

For example, if a data source has the id 51c99cfdf91b28459f976414, it can be retrieved as follows:

```javascript
var mySpecificDatasource;
api.getDatasource( '51c99cfdf91b28459f976414', function( datasource ) { mySpecificDatasource = datasource; } );
```

Getting a Group of Variables From a Data source
-------------------------------------------------

You can also retrieve some or all of the variables of a data source:

```javascript
var allVariablesOfDatasource;
myDatasource.getVariables( function( variables ) { allVariablesOfDatasource = variables; } );
```

Getting a Specific Variable
----------------------------

As with data sources, you can use your variable's ID to retrieve the details about it:

```javascript
var mySpecificVariable;
api.getVariable( '56799cf1231b28459f976417', function( variable ) { mySpecificVariable = variable; } );
```
