from core import Pyhandler

test = Pyhandler()

#test.login("alanopi.314@gmail.com","password1234")

while True:
    selection = raw_input("1. Login\n2.Logout\n3.Check Login\n")
    if selection == "1":
        test.login("alanopi.314@gmail.com","password")

    elif selection == "2":
        test.logout()

    elif selection == "3":
        test.session_check()