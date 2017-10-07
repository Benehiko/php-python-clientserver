import requests


class Pyhandler:
    def __init__(self):
        self.url = "http://sepam.anzen-learning.xyz/"
        self.token = ""
        self.id = ""


    def post(self, file, data):
        newurl = self.url + file
        r = requests.post(newurl, data)

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

        if len(return_data) > 0:
            self.set_details(return_data)
            print("Logged in")
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
        data = {"sessionid": self.get_token()}
        if self.post("logout.php", data):
            print("logged out")
            return True
        print("something went wrong")
        return False

    def set_details(self, data):
        self.token = data[0]
        self.id = data[1]
        return True

    def get_details(self):
        data = {"id": self.id, "token": self.token}
        return data
