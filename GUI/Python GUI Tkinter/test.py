from core import Pyhandler
import sys, json

test = Pyhandler()

#test.login("ater13@student.monash.edu", "password1234!")
#test.login("benehiko@gmail.com","password123!")
#test.login("alanopi.314@gmail.com","password123!")
#test.login("admin132@monash.edu.12", "password123!")

def login_selection():
    if test.login("ater13@student.monash.edu", "password1234!"):
        loginselect = raw_input("1.Create Room\n2.List Rooms\n3.Add User\n4.Upload Mark\n5.Commit Data\n6.Go back to main\n")
        if loginselect == "1":
            roomName = "TestRoom"
            test.create_room(roomName)
        elif loginselect == "2":
            list_rooms()
        elif loginselect == "3":
            test.add_user_room(60,51)
        elif loginselect == "4":
            test.add_mark(60,40)
        elif loginselect == "5":
            data = json.loads(test.get_data())
            userID = test.get_id()
            roomID = data["RoomDetails"]["RoomID"]
            comment = "This is a test comment"
            description = "This is a test comment"
            test.commit_data(userID,roomID,comment,description)
        elif loginselect == "6":
            main_menu()

def room_management():
    selection = raw_input("Select Room")
    data = test.get_data()

def list_rooms():
    data = test.get_data()
    jsondata = json.loads(data)
    #rooms = jsondata["Rooms"]
    print(jsondata)

def main_menu():
    while True:
        selection = raw_input("1.Login\n2.Logout\n3.Check Login\n4.Get Data\n5.Register\n6.Exit\n")
        if selection == "1":
            login_selection()

        elif selection == "2":
            test.logout()

        elif selection == "3":
            test.session_check()

        elif selection == "4":
            print(test.get_data())
        elif selection == "5":
            #print("ater13@student.monash.edu", "password1234!")
            test.register("ater13@student.monash.edu", "password123!")
            test.register("benehiko@gmail.com","password123!")
            test.register("alanopi.314@gmail.com","password123!")
            test.register("admin132@monash.edu.12", "password123!")

        elif selection == "6":
            print("Exiting...")
            sys.exit()

main_menu()