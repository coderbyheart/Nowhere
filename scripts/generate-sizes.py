import os
import subprocess

mediadir = "./htdocs/media/stones/"

for file in os.listdir(mediadir):
    print(file)
