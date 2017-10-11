
# The purpose of this class is to handle everything between the client and the server.
# This file was setup by Alano Terblanche and edited by Mohamed Mamdouh

import requests, json, sys


class Pyhandler:

    #Do not change anything in __init__ as it is initialising variables.
    def __init__(self):
        self.url = "http://sepam.anzen-learning.xyz/"
        self.token = ""
        self.id = ""
        self.AccountType = ""
        self.AccountTypeID = ""
        #These headers are used to set the format in which our requests operate to the webserver. It is needed
        #to prevent server side errors as this programme immitates a browser.
        self.headers = {
            "User-Agent": "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0",
            "Accept-Encoding": "gzip, deflate",
            "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            "Accept-Language": "en-US,en;q=0.5",
            "Connection": "keep-alive"
        }

    #Post function was setup to ensure a quick call and setup to the server
    #Post will return False if the server throws anything other than code 200
    #else it will just return the server side echo text.
    def post(self, file, data):
        newurl = self.url + file
        r = requests.post(newurl, data, headers=self.headers)
        if r.status_code == 200:
            return r.text
        else:
            print("Check your connection")
            return False

    #Register - this function is for registering a user to the database.
    #It does not have any direct contact to the database, it only posts data to php.
    def register(self, username, password):
        data = {"username": username, "password": password}
        print(data)
        msg = self.post("register.php",data)
        if msg == "1":
            print("Registration success")
            return True
        print(msg)
        return False

    #Login - ensures that we can retrieve a token (for session) from the server. This token is destroyed once the
    #application is closed. It gets created server side.
    def login(self, username, password):
        if not self.session_check():
            data = {"username": username, "password": password}
            return_data = self.post("login.php", data)
            try:
                self.set_details(return_data)
                print("Logged in\nID: ", str(self.id), "\nToken: ", str(self.token))
                return True
            except ValueError:
                print(return_data)
        return True
            # if len(return_data) > 0:
            #
            # else:
            #     print(return_data)
            #     return False

    #Session_check Called everytime an action gets called which requires the user to be logged in.
    #This function is only for validating client side token data with the server.
    def session_check(self):
        data = self.get_details()
        msg = self.post("checklogin.php", data)
        if msg == "1":
            print("Logged in")
            return True
        print("Logged out")
        return False

    #Logout - Destroys the token and all evidence of it.
    def logout(self):
        data = {"id": self.get_id(), "token": self.get_token()}
        msg = self.post("logout.php", data)
        if msg == "1":
            data = {"token":"","id":""}
            self.set_details(data)
            print("logged out")
            return True
        else:
            print(msg)
        print("something went wrong")
        return False

    #Ensures that the token gets used and stored client side
    def set_details(self, data):
        print(data)
        json_data = json.loads(data)
        self.token = json_data["token"]
        self.id = json_data["id"]
        self.AccountType = json_data["GroupDescription"]
        self.AccountTypeID = json_data["GroupID"]
        return True

    #get_details simply accesses our class level variables for the current sessions user id and access token.
    def get_details(self):
        data = {"id": self.get_id(), "token": self.get_token()}
        return data

    #get_token is for getting the current session token
    def get_token(self):
        return self.token

    #get_id is for getting the current session user id
    def get_id(self):
        return self.id

    #get groupID for this user
    def get_groupID(self):
        return self.AccountTypeID

    #get_data is for getting the current user data - does not matter if user is student or admin.
    def get_data(self):
        data = {"id": self.id, "token": self.token}
        msg = self.post("userdata.php", data)
        if msg == "false":
            print("Could not retrieve data")
            return False
        else:
            data = msg
            return data

    #update_data is to update any all user data.
    def update_data(self, data):
        self.post("datahandler.php",data)
        return True

    #create_room will create a room for users to be added in for their project. It only needs the room name as the
    #"data" parameter
    def create_room(self, roomname):
        if (self.session_check()):
            room_details = {"Action": "CreateRoom", "roomName":roomname,"ownerID":self.get_id()}
            msg = self.post("datahandler.php",room_details)
            if msg == "1":
                print("Room with name: ",roomname," created")
                print(msg)
                return True
            print(msg)
        return False

    def add_user_room(self, userID, roomID):
        if (self.session_check()):
            data = {"Action":"AddUserRoom","roomID": roomID, "UserID": userID}
            if self.post("datahandler.php", data) == "1":
                print("User added to room ",roomID)
                return True
        return False

    def add_mark(self,userID, mark):
        if (self.session_check()):
            data = {"Action":"AddMarks","userID":userID,"mark":mark}
            msg = self.post("datahandler.php", data)
            if msg == "1":
                print("User mark was added")
                return True
            print(msg)
            return False

    def remove_user(self, userID, roomID):
        if self.session_check():
            data = {"Action": "RemoveUser", "UserID":userID, "roomID":roomID}
            if self.post("datahandler.php",data):
                return True
        return False

    def commit_data(self, userID, roomID, comment, description):
        if self.session_check():
            data = {"Action":"CommitData", "userID":userID, "roomID":roomID, "comment" : comment, "description": description}
            msg = self.post("datahandler.php",data)
            if  msg == "1":
                print(msg)
                return True
        return False
