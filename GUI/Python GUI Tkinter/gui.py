from tkinter import *
from PIL import Image, ImageTK
from core import Pyhandler

class Gui():

    def __init__(self):
        self.root = TK()
        self.core = Pyhandler()


    def login(self):
        A = Label(login, text="Enter Username:", font="system 20")
        B = Entry(login, bd=5)
        C = Button(login, text="Submit", font="system 20 bold", command=self.core.login(B.get(), E.get()))
        D = Label(login, text="Enter Password:", font="system 20")
        E = Entry(login, bd=5, show="*")

        A.grid(row=0, sticky="e")
        B.grid(row=0, column=1)
        D.grid(row=1, sticky="e")
        E.grid(row=1, column=1)
        C.grid(row=3, columnspan=2)
        self.root.mainloop()

    def register(self):
        A = Label(reg, text="Enter Username:", font="system 20")
        B = Entry(reg, bd=5)
        D = Label(reg, text="Enter Password:", font="system 20")
        E = Entry(reg, bd=5, show="*")
        F = Label(reg, text="Confirm Password:", font="system 20")
        G = Entry(reg, bd=5, show="*")
        C = Button(reg, text="Register", font="system 20 bold", command=self.core.register(B.get(),E.get()))
        A.grid(row=0, sticky="e")
        B.grid(row=0, column=1)
        D.grid(row=1, sticky="e")
        E.grid(row=1, column=1)
        F.grid(row=2, sticky="e")
        G.grid(row=2, column=1)

        C.grid(row=3, columnspan=2)
        self.root.mainloop()

    def adminPage(self):
        self.root = TK()
        self.root.title("Homepage")
        self.root.geometry("1000x800")

        rooms = []
        data = self.core.get_data()
        username = self.core.getUsername()

        def showgroup(event):
            selection = str(event)
            return selection

        background_image = Image.open('EG_Logo.jpg')
        background_image = background_image.resize((250, 250))
        photo = ImageTk.PhotoImage(background_image)
        z = Label(homepage, image=photo)
        z.place(x=10, y=10)
        z.image = background_image

        frame1 = Frame(homepage)
        frame1.pack()
        frame2 = Frame(homepage)
        frame2.pack()
        frame3 = Frame(homepage)
        frame3.pack(ipady=0)
        frame4 = Frame(homepage)
        frame4.pack()

        a = Label(frame1, text="R8ME", font="device 30 bold", height=2, fg="blue")
        b = Label(frame1, text="Admin name"+username, font="device 15")
        d = Label(frame2, text="Available groups:", bd=20, font="ansi 20")

        h = Button(frame2, text="Mark", font="system 20 bold", command=markPage(selection) if selection not None else messagebox.showinfo("Selection", "Nothing selected"), fg="red")
        i = Listbox(frame2, bd=2, selectmode=SINGLE, font="ansi 15")

        counter = 0

        for value in data['RoomDetails']:
            i.insert(counter, value['RoomName'])

        selection = i.bind("<Button-1>", showgroup)

        # e = Label(frame3, text="Selected group:", font="ansi 20")
        # e.pack(side=LEFT)


        a.pack()
        b.pack()
        d.pack(side=TOP)
        h.pack(side=RIGHT, padx=20)
        i.pack(side=RIGHT, ipadx=50, pady=40)

        self.root.mainloop()

    def markPage(self, selection):

        def commentsubmitted():
            comment = g.get()
            j.insert(0, comment)
            id = self.core.get_id()
            students = []
            roomID = ''
            data = self.core.get_data()
            for value in data['RoomData']:
                if value['RoomName'] == selection:
                    rooomID = value['RoomID']
                    for student in value['Students']:
                        students.append(student['Username'])

            self.core.commit_data(id, roomID, comment, "Comment")
            messagebox.showinfo("Comment", "Your comment has been submitted")

        def marksubmitted():
            messagebox.showinfo("Mark", "Marks have been successfully inputted :D")

        # background_image = Image.open('EG_Logo.jpg')
        # background_image = background_image.resize((300, 250))
        # photo = ImageTk.PhotoImage(background_image)
        # z = Label(markpage, image=photo)
        # z.place(x=10, y=500)
        # z.image = background_image


        frame1 = Frame(markpage)
        frame1.pack()
        frame2 = Frame(markpage)
        frame2.pack(ipady=20)
        frame3 = Frame(markpage)
        frame3.pack(ipady=20)
        frame4 = Frame(markpage)
        frame4.pack()

        a = Label(frame1, text="R8ME", font="device 30 bold", height=2, fg="blue")
        b = Label(frame1, text="Admin name", font="device 15")
        d = Label(frame2, text="Students:", bd=20, font="ansi 20")
        e = Label(frame3, text="Add a comment:", font="ansi 20")
        f = Label(frame4, text="Comments:", font="ansi 20")
        l = Button(frame3, text="Submit", font="system 20 bold", command=commentsubmitted)
        g = Entry(frame3, bd=3)

        i = Listbox(frame2, bd=2)
        i.insert(0, "Student 1")
        i.insert(1, "Student 2")
        j = Listbox(frame4, bd=2)
        k = Scrollbar(j)

        m = Button(frame2, text="Enter", font="system 10 bold", command=marksubmitted)
        m.pack(side=RIGHT, padx=10)
        h = Entry(frame2, bd=3, width=5)
        h.pack(side=RIGHT)
        x = Label(frame2, text="Individual marks:", font="ansi 15")
        x.pack(side=RIGHT, padx=10)
        y = Label(frame2, text="Total Mark:", font="device 20 bold")
        y.pack(side=BOTTOM)

        a.pack()
        b.pack()
        # C.pack(side=RIGHT)
        d.pack(side=LEFT)
        e.pack(side=LEFT)
        f.pack()
        l.pack(side=RIGHT, padx=20)

        g.pack(side=LEFT)
        i.pack(side=RIGHT, ipadx=50)
        j.pack(ipadx=150, ipady=100)
        k.pack(side=RIGHT, fill=Y)

        markpage.mainloop()