from tkinter import *

def runRegister():
    reg = Tk()

    def getUsernameandPassword():
        userpass = ()
        username = B.get()
        password = E.get()
        confirmpassword = G.get()
        if password == confirmpassword:
            userpass = (username, password)

        print(userpass)
        return userpass

    A = Label(reg, text="Enter Username:", font="system 20")
    B = Entry(reg, bd=5)
    D = Label(reg, text="Enter Password:", font="system 20")
    E = Entry(reg, bd=5, show="*")
    F = Label(reg, text="Confirm Password:", font="system 20")
    G = Entry(reg, bd=5, show="*")

    C = Button(reg, text="Register", font="system 20 bold", command=getUsernameandPassword)


    A.grid(row=0, sticky="e")
    B.grid(row=0, column=1)
    D.grid(row=1, sticky="e")
    E.grid(row=1, column=1)
    F.grid(row=2, sticky="e")
    G.grid(row=2, column=1)

    C.grid(row=3, columnspan=2)




    reg.mainloop()
