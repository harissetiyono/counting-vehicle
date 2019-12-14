import os
import time

run = True
while run:
 os.system('php artisan db:seed --class=countingSeeder')
 time.sleep(3)
 
