import json
import time
import sys
from bresenham import bresenham
import argparse

coordinate = json.loads(sys.argv[1])
hasil = []
try:
    for c in coordinate:
        hasil.extend(list(bresenham(c[0], c[1], c[2], c[3])))

    with open(sys.argv[4]+ '/' + sys.argv[2] + '/' + sys.argv[2] + '_line_'+sys.argv[3] + '.json', 'w') as json_file:
        json.dump(hasil, json_file)
    print('success generate line')
except:
    print('failed generate line')

# with open('lineIN.json') as json_file:
#     data = json.load(json_file)
