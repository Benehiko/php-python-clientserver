import urllib2, requests

class Pyhandler:


    def __init__(self):
        self.url = "http://sepam.anzen-learning.xyz/"

    def get(self,file):
        content = urllib2.urlopen(self.url+file)

    def post(self,file,data):
        newurl = self.url+file
        print(newurl)
        r = requests.post(newurl,data)
        print(r)
        if r.text == "true":
            print(r.text)
            return True
        else:
                print(r.text)
                return False

    def register(self,username,password):
        data = {"username":username,"password":password}
        if (self.post("register.php",data)):
            print("Registration success")
            return True
        return False

    def login(self,username,password):
        data = {"username":username,"password":password}
        if (self.post("login.php",data)):
            return True
        return False