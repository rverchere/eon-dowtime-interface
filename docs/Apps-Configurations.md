# Applications Configuration

## Introduction

The formation for configurations file was selected to be yaml.  
This decision was motivated to give simple format for non experts users.

Yaml provide simple format to read and write configuration.

In this file, you'll find three sections to describe application components them could be downtimed.

## Sections

All of these three sections was needed to could be used by interface.

### Display Name

This is a simple section composed by simple variable.  
This variable is only used to displaying in pages.

Utility is only to provide an explicit name for non technical users.

#### Example

```yaml
displayname: "Testing application"
```

### App

App section define host and service into EyesOfNetwork was used to check Business Process at high level.  
This could not be multivalued, you could only define one application service by configuration file.

Downtime high level application permit to disable app notification.

#### Example

```yaml
app:
  - host: Application_BP
    service: test
```

### Host

Host section define all unitary checkpoint into EyesOfNetwork was composing high level application.  
This section could be multivalued, for hosts and services.

You must specify all unitary checkpoint needed to downtime application here.  
The interest of this section is directly linked for EyesOfReport component to don't generate outages into report.

Composition of hosts array :
- host: Define equipment into EyesOfNetwork configuration which hold service checkpoints. This host will automaticaly take downtime on declaration.
- services: This array is by host declaration. This define all unitary checkpoint will be downtimed on declaration.
- propagation_childs: This is a simple boolean value. For v1.0.0, this entry was not managed. But on later version, this will be permit to downtime all host childs at downtime application.

To downtime a host without services checkpoint, you've just to set __host__ entry without services.

#### Example

Example for two host, __localhost__ with services and __test_host__ without services.

```yaml
hosts:
  - host: localhost
    services:
      - memory
      - partitions
      - processor
      - systime
      - uptime
    propagation_childs: false
  - host: test-host
    propagation_childs: false
```