from core import Pyhandler

test = Pyhandler()

#test.login("alanopi.314@gmail.com","password1234")

while True:
    selection = input("1.Login\n2.Logout\n3.Check Login\n4.Register")
    if selection == "1":
        test.login("alanopi.314@gmail.com","password1234")

    elif selection == "2":
        test.logout()

    elif selection == "3":
        test.session_check()
    elif selection == "4":
        username = input("Enter username: ")
        password = input("Enter password: ")
        test.register(username,password)