import requests, json


class Pyhandler:
    def __init__(self):
        self.url = "http://sepam.anzen-learning.xyz/"
        self.token = ""
        self.id = ""
        self.headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0",
        "Accept-Encoding": "gzip, deflate",
        "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
        "Accept-Language": "en-US,en;q=0.5",
        "Connection": "keep-alive"
        }



    def post(self, file, data):
        newurl = self.url + file
        r = requests.post(newurl, data, headers = self.headers)
        if r.status_code == 200:
            return r.text
        else:
            print("Check your connection")
            return False

    def register(self, username, password):
        data = {"username": username, "password": password}
        print(data)
        if self.post("register.php", data) == "true":
            print("Registration success")
            return True
        return False

    def login(self, username, password):
        data = {"username": username, "password": password}
        return_data = self.post("login.php", data)
        if return_data == False:
            return False
        if len(return_data) > 0:
             self.set_details(return_data)
             print("Logged in")
             print(self.get_token())
             return True
        else:
             print(return_data)
             return False


    def session_check(self):
        data = self.get_details()
        msg = self.post("checklogin.php", data)
        print(msg)
        if msg == "true":
            print("logged in")
            return True
        print("Not logged in")
        return False

    def logout(self):
        data = {"id":self.get_id(),"token": self.get_token()}
        msg = self.post("logout.php", data)
        if msg == "True":
            print("logged out")
            return True
        else:
            print(msg)
        print("something went wrong")
        return False

    def set_details(self, data):
        print(data)
        self.token = data.token
        self.id = data.id
        return True

    def get_token(self):
        return self.token

    def get_id(self):
        return self.id

    def get_details(self):
        data = {"id": self.id, "token": self.token}
        return data

    def get_data(self):
        data = {"id": self.id, "token": self.token}
        msg = self.post("userdata.php",data)
        if msg != False:
            data = msg
            return data
        return False