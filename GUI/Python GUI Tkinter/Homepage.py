from tkinter import *
from tkinter import messagebox
from PIL import Image,ImageTk



def runHomepage():
    homepage = Tk()
    homepage.title("Homepage")
    homepage.geometry("1000x700")

    def commentsubmitted():
        comment = G.get()
        J.insert(0, comment)
        messagebox.showinfo("Comment", "Your comment has been submitted")

    background_image = Image.open('EG_Logo.jpg')
    background_image = background_image.resize((300, 250))
    photo = ImageTk.PhotoImage(background_image)
    Z = Label(homepage, image=photo)
    Z.place(x=10, y=10)
    Z.image = background_image

    frame1 = Frame(homepage)
    frame1.pack()
    frame2 = Frame(homepage)
    frame2.pack()
    frame3 = Frame(homepage)
    frame3.pack(ipady=20)
    frame4 = Frame(homepage)
    frame4.pack()



    A = Label(frame1, text="R8ME", font="device 30 bold", height=2)
    B = Label(frame1, text="Lecturer name", font="device 15")
    #C = Label(frame2, text="Student ID", font="device 15")
    D = Label(frame2, text="Your group members:", bd=20, font="ansi 20")
    E = Label(frame3, text="Add a comment:", font="ansi 20")
    F = Label(frame4, text="Comments:", font ="ansi 20")

    G = Entry(frame3, bd=3)
    comment = G.get()
    H = Button(frame3, text="Submit", font="system 20 bold", command=commentsubmitted)
    I = Listbox(frame2, bd=2)
    J = Listbox(frame4, bd=2)
    K = Scrollbar(J)


    A.pack()
    B.pack()
    #C.pack()
    D.pack()
    E.pack(side=LEFT)
    F.pack()

    H.pack(side=RIGHT, padx=20)
    G.pack(side=LEFT)
    I.pack(side=BOTTOM, ipadx=50)
    J.pack(ipadx=150)
    K.pack(side=RIGHT, fill=Y)


    homepage.mainloop()


runHomepage()