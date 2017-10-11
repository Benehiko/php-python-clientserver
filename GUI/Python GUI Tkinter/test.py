from core import Pyhandler
import sys

test = Pyhandler()

#test.login("benehiko@gmail.com","pass123!")
#test.login("alanopi.314@gmail.com","password1234"):

while True:
    selection = raw_input("1.Login\n2.Logout\n3.Check Login\n4.Get Data\n5.Register\n6.Exit\n")
    if selection == "1":
        if test.login("ater13@student.monash.edu","password1234!"):
            loginselect = raw_input("1.Create Room\n")
            if loginselect == "1":
                roomName = "TestRoom"
                test.create_room(roomName)

    elif selection == "2":
        test.logout()

    elif selection == "3":
        test.session_check()

    elif selection == "4":
        print(test.get_data())
    elif selection == "5":
        print("ater13@student.monash.edu","password1234!")
        test.register("ater13@student.monash.edu","password1234!")

    elif selection == "6":
        print("Exiting...")
        sys.exit()