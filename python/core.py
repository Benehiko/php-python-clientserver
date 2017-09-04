import urllib2, requests

class Pyhandler:

    def __init__(self):
        self.url = "http://sepam.anzen-learning.xyz/"

    def get(self,file):
        content = urllib2.urlopen(self.url+file)

    def post(self,file,data):
        r = requests.post(self.url+file,data)
        if (r.status_code == 200):
            if (r.text == "true"):
                return true
            else:
                print(r.text)
                return false
        else:
            return false

    def register(self,username,password):
        data = {"username":username,"password":password}
        if (self.post("register.php",data)):
            return true
        return false

    def login(self,username,password):
        data = {"username":username,"password":password}
        if (self.post("login.php",data)):
            return true
        return false




