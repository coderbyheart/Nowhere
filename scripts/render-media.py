import os
import subprocess

srcdir = "/mnt/extra/Dropbox/Projekte/nowhere/nowhere_daten_einzeln"
mediadir = "/home/m/www/nowhere/htdocs/media/stones/"

for file in os.listdir(srcdir):
    if (os.path.isdir(srcdir + os.path.sep + file)):
        stonefile = srcdir + os.path.sep + file + os.path.sep + "stone.png"
        placefile = srcdir + os.path.sep + file + os.path.sep + "place.jpg"
        # Stones
        for size in ['150', '1024']:
            stonesizefile = mediadir + file + "-stone-" + size + ".png"
            if not os.path.isfile(stonesizefile):
                subprocess.call(["/usr/bin/convert", "-background", "transparent", "-scale", "%sx%s" % (size, size), '-gravity', 'center', '-extent', "%sx%s" % (size, size), stonefile, stonesizefile])
                print(stonesizefile)
            if size is '150':
                placesizefile = mediadir + file + "-place-" + size + ".png"
            else:
                placesizefile = mediadir + file + "-place-" + size + ".jpg"
            if not os.path.isfile(placesizefile):
                subprocess.call(["/usr/bin/convert", "-background", "transparent", "-scale", "%sx%s" % (size, size), '-gravity', 'center', '-extent', "%sx%s" % (size, size), placefile, placesizefile])
                print(placesizefile)
