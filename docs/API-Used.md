# API Calls

This interface use EyesOfNetwork and Thruk API.

To get existings downtimes, this interface call EyesOfNetwork API.

The API called with POST method is :

```

/eonapi/listNagiosObjects

```

For this usage, this API was called by authentication with username and APIkey set directly into URL call :

```

/eonapi/listNagiosObjects?&username=<username>&apiKey=<eon_apikey>

```

To get needed informations, the JSON used is :

```json

{
  "object": "downtimes",
  "columns": [
    "host_name",
    "service_description",
    "comment",
    "entry_time",
    "start_time",
    "end_time"
  ],
  "backendid": "0",
  "filters": [
    "host_name = <hostname>",
    "service_description = <servicename>"
  ]
}

```

To set downtime, the v1.0.0 use Thruk API called with POST method.

Two API is called dependant of host or service downtime would be applied.

__Host downtime API__:

```

/thruk/r/hosts/<hostname>/cmd/schedule_host_downtime

```

__Service downtime API__:

```

/thruk/r/services/<hostname>/<servicename>/cmd/schedule_svc_downtime

```

All theses two API need informations to work provided by JSON :

```json
{
  [
    'comment_data': '<Downtime description>',
    'comment_author': '$dwt_author'
  ]
}
```

Note : The value of __$dwt\_author__ is set into __config.php__ file.