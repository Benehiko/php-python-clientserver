from tkinter import *

def runHomepage():
    homepage = Tk()
    homepage.geometry("1000x700")
    frame1 = Frame(homepage)
    frame1.pack()
    frame2 = Frame(homepage)
    frame2.pack()
    frame3 = Frame(homepage)
    frame3.pack()
    frame4 = Frame(homepage)
    frame4.pack(side=BOTTOM)


    A = Label(frame1, text="Welcome to EG's group marking application", font="device 30 bold", height=3)
    B = Label(frame2, text="Student name", font="device 15")
    C = Label(frame2, text="Student ID", font="device 15")
    D = Label(frame3, text="Your group members:", bd=100, font="ansi 20")
    E = Label(frame3, text="Your overall mark:", bd=100, font="ansi 20")
    F = Label(frame4, text="Session status", justify=RIGHT)
    A.pack()
    B.pack()
    C.pack()
    D.pack(side=LEFT)
    E.pack(side=RIGHT)
    F.pack()


    homepage.mainloop()

runHomepage()
