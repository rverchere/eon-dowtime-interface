# User downtime interface

## Generic informations

### Application

- Host : Applications_Building
  - Service :
    - Test-int-downtime

### Control point

- Host1 : Test-int-downtime1
  - Services :
    - memory
    - partition
    - processor
    - systime
    - uptime

- Host2 : Test-int-downtime2
  - Services :
    - memory
    - partition
    - processor
    - systime
    - uptime

## Needs expression

Create simple web interfaces to create applications downtime (high and low level) into EyesOfNetwork throught REST API, NRDP or Thruk API.

Interface format (simple description)
```
<Nom application>      <Champ texte de description du downtime>  <date/heure de début> <date/heure de fin> <bouton valider>
```

The interface should mask all downtime processing and generate report on applicated downtimes.

## How ?

The web interface should call script will permit do creation downtimes into monitoring app.
It will be necessary to create code fully generic to correspond to all use cases.

Functionnaly, it should be necessary to use parameters file collection defining content and ensemble of targets them receive the needed downtime.
This file will be unique by application.
By préférence, it will be at yaml format to give good readability and facility to modify.

### Configuration file

This file should contain the ensemble of next's informations :

- Application name (Business Process name)
- Host will permit BP check into monitoring process
- Service according to BP name
- Unitary hostnames composing applications
- Unitary services linked to host.

#### YAML Format Proposition

```yaml
- app: <nom application>
- host: <application_host>
  services:
    - Application1
  - propagation_enfants: true/false
- host: hostapp1
  services:
    - memory
    - partitions
    - processor
    - systime
    - uptime
  - propagation_enfants: true/false
- host: hostapp2
  services:
    - memory
    - partitions
    - processor
    - systime
    - uptime
  - propagation_enfants: true/false
```