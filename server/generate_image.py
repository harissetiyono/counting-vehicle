import numpy as np
import argparse
import cv2

ap = argparse.ArgumentParser()

ap.add_argument("-u", "--url", required=True, help="Url address video stream")
ap.add_argument("-p", "--port", required=True, help="port stream for video process")
ap.add_argument("-l", "--location", required=True, help="location")

args = vars(ap.parse_args())

url = str(args["url"])
port = str(args["port"])
location = str(args["location"])

cp = cv2.VideoCapture(url)
ok, img = cp.read()
cv2.imwrite(location + port + '.jpg', img)
print("sukses")