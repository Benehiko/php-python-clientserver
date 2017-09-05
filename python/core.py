import urllib2, requests

class Pyhandler:



    def __init__(self):
        self.url = "http://sepam.anzen-learning.xyz/"
        self.token = ""

    def get(self,file):
        content = urllib2.urlopen(self.url+file)

    def post(self,file,data):
        newurl = self.url+file
        r = requests.post(newurl,data)
        if r.status_code == 200:
            return r.text
        else:
            print("Check your connection")
            return False

    def register(self,username,password):
        data = {"username":username,"password":password}
        if self.post("register.php", data) == "true":
            print("Registration success")
            return True
        return False

    def login(self,username,password):
        data = {"username":username,"password":password}
        token = self.post("login.php", data)
        if token != False:
            self.set_token(token)
            print("Logged in")
            return True
        print("Could not login")
        return False

    def session_check(self):
        data = {"sessionid":self.get_token()}
        if self.post("checklogin.php",data):
            print("logged in")
            return True
        print("Not logged in")
        return False

    def logout(self):
        data = {"sessionid":self.get_token()}
        if(self.post("logout.php",data)):
            print("logged out")
            return True
        print("something went wrong")
        return False

    def set_token(self,token):
        self.token = token
        return True

    def get_token(self):
        return self.token
