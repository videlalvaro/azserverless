# Azure Serverless #

This projects provides a PHP [Custom Handler](https://docs.microsoft.com/azure/azure-functions/functions-custom-handlers?WT.mc_id=data-11039-alvidela) for Azure Functions.

## Installation ##

In your composer.json add the following dependency:

```json
"require": {
        "videlalvaro/azserverless": "*"
}
```

Then run:

```bash
composer update
```

Then start an Azure Functions project. In your `host.json` add the following:

```json
"customHandler": {
    "description": {
        "defaultExecutablePath": "php",
        "arguments": [
            "-S",
            "0.0.0.0:%FUNCTIONS_CUSTOMHANDLER_PORT%",
            "vendor/videlalvaro/azserverless/bin/serverless.php"
        ]
    },
    "enableForwardingHttpRequest": false
},
```

See `host.sample.json` for an example of how this file should look like.

Finally, make copy the file `local.settings.sample.json` into your project and call it `local.settings.json`. Adapt the contents to include your connection strings in the `AzureWebJobsStorage` field.

## Usage ##

In your Azure Functions project you will have one folder per serverless function. 

To create a function called `HttpTrigger`, create a folder with the same name, then inside add two files: `function.json` and `index.php`. Here's their content:

```json
{
  "disabled": false,
  "bindings": [
    {
      "authLevel": "anonymous",
      "type": "httpTrigger",
      "direction": "in",
      "name": "req"
    },
    {
      "type": "http",
      "direction": "out",
      "name": "$return"
    }
  ]
}
```

And the corresponding index.php file:

```php
<?php
use Azserverless\Context\FunctionContext;

function run(FunctionContext $context) {

    $req = $context->inputs['req'];

    $context->log->info('Http trigger invoked');

    $query = json_decode($req['Query'], true);

    if (array_key_exists('name', $query)) {
        $message = 'Hello ' . $query['name'] . '!';
    } else {
        $message = 'Please pass a name in the query string';
    }

    return [
        'body' => $message,
        'headers' => [
            'Content-type' => 'text/plain'
        ]
    ];
}
?>
```

As you can see functions are provided with a `FunctionContext` object where they can access `request` data, and also log information to the console.

To learn more about the details of serverless on Azure, take a look at the [Azure Functions documentation](https://docs.microsoft.com/azure/azure-functions/create-first-function-vs-code-node?WT.mc_id=data-11039-alvidela).

## Deployment ##

Follow the instructions here to enable PHP 7.4 & make sure composer is run while deploying your function app to Azure: [Configure a PHP app for Azure App Service](https://docs.microsoft.com/azure/app-service/configure-language-php?pivots=platform-windows&WT.mc_id=data-11039-alvidela#set-php-version)

When the options for Azure CLI require a `--name` option, provide your Azure Functions app name.
