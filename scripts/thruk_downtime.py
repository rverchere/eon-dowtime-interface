#!/usr/bin/env python
#
# Set Thruk Downtime
#
# 2018-11-07 Remi Verchere <remi.verchere@axians.com>

import argparse
import requests
import urllib3
import simplejson as json
from pprint import pprint

# Disable SSL Warning
requests.packages.urllib3.disable_warnings()
urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

headers = {
    'Content-Type': 'application/json',
}

# Static cookies for EON web auth
cookie = {
    'user_name': 'admin',
    'session_id': '169014757',
    'user_id': '1',
    'group_id': '1',
    'user_limitation': '0'
    }

def thrukGetDowntimes(server):
    r_downtimes = requests.get('https://%s/thruk/r/downtimes' % server,
                                cookies=cookie, headers=headers)
    if r_downtimes.status_code == 200:
        return (r_downtimes)
    else:
        print 'ERROR, cannot get downtime values'
        return None

def thrukSetHostDowntime(server, host):
#   %> curl -d "start_time=now" -d "end_time=+60m" -d "comment_data='downtime comment'" http://0:3000/thruk/r/services/<host>/<svc>/cmd/schedule_svc_downtime
    downtime = {
        'comment_data': 'Python test'
    }
    r_exec_downtimes = requests.post('https://%s/thruk/r/hosts/%s/cmd/schedule_host_downtime' % (server, host),
                                        cookies=cookie, headers=headers,
                                        data=json.dumps(downtime))

    pprint(r_exec_downtimes.content)

if __name__ == '__main__':
    parser = argparse.ArgumentParser(description='Play with thruk API with EON, setting downtimes')
    parser.add_argument('-S', '--server', type=str, help='EON server', required=True)
    parser.add_argument('-H', '--hostname', type=str, help='Host to set downtime')
    parser.add_argument('-s', '--service', type=str, help='Service to set downtime')
    parser.add_argument('-l', '--list', action="store_true", help='Get Downtime')

    args = parser.parse_args()

    if args.list:
        result = thrukGetDowntimes(args.server)
        if result:
            pprint(result.json())
