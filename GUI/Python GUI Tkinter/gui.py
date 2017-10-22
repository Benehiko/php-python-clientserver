from tkinter import *
from tkinter import messagebox

# from PIL import Image, ImageTK
from core import Pyhandler


class Gui():
    def __init__(self):
        self.root = Tk()
        self.core = Pyhandler()

    def startup(self):
        self.root.destroy()
        self.root = Tk()
        self.root.title("EG's group marking application")

        def getRegister():
            self.root.rootister()

        def getLogin():
            self.login()

        def showHelp():
            self.root.messagebox.showinfo("Help",
                                          "If you are a new user, click on self.rootister to create an account. If you already have an account, click on Log in.")

        A = Button(self.root, text="?", command=showHelp, font="comic 15 bold", fg="blue")
        B = Button(self.root, text="Register", font="system 40 bold", command=getRegister)
        C = Button(self.root, text="Log In", font="system 40 bold", command=getLogin)

        A.grid(row=0)
        B.grid(row=1)
        C.grid(row=2)

        self.root.mainloop()

    def login(self):
        self.root.destroy()
        self.root = Tk()
        self.root.title("Login")

        def getDetails():
            username = "ater13@student.monash.edu"  # B.get()
            password = "password1234!"  # E.get()
            if self.core.login(username, password):
                print("Account ID", self.core.get_groupID())
                if self.core.get_groupID() == 1:
                    self.adminPage()
                else:
                    self.studentPage()
            else:
                messagebox.showinfo("Information", "The details entered is incorrect. Please try again")

        A = Label(self.root, text="Enter Username:", font="system 20")
        B = Entry(self.root, bd=5)

        D = Label(self.root, text="Enter Password:", font="system 20")
        E = Entry(self.root, bd=5, show="*")

        A.grid(row=0, sticky="e")
        B.grid(row=0, column=1)
        D.grid(row=1, sticky="e")
        E.grid(row=1, column=1)
        C = Button(self.root, text="Submit", font="system 20 bold", command=getDetails)
        C.grid(row=3, columnspan=2)
        self.root.mainloop()

    def register(self):
        self.root.destroy()
        self.root = Tk()
        self.root.title("self.rootister")

        def getDetails():
            self.core.register(B.get(), E.get())
            return True

        A = Label(self.root, text="Enter Username:", font="system 20")
        B = Entry(self.root, bd=5)
        D = Label(self.root, text="Enter Password:", font="system 20")
        E = Entry(self.root, bd=5, show="*")
        F = Label(self.root, text="Confirm Password:", font="system 20")
        G = Entry(self.root, bd=5, show="*")

        A.grid(row=0, sticky="e")
        B.grid(row=0, column=1)
        D.grid(row=1, sticky="e")
        E.grid(row=1, column=1)
        F.grid(row=2, sticky="e")
        G.grid(row=2, column=1)
        C = Button(self.root, text="Register", font="system 20 bold", command=getDetails)
        C.grid(row=3, columnspan=2)
        self.root.mainloop()

    def adminPage(self):
        self.root.destroy()
        self.root = Tk()
        self.root.title("Admin Page")
        self.root.geometry("1000x800")

        rooms = []
        data = self.core.get_data()
        username = self.core.getUsername()
        self.selection = ""

        def showgroup(event):
            w = event.widget
            line = w.curselection()
            print(line)
            index = int(w.curselection()[0])
            value = w.get(index)
            print("Listbox slection: ", value)
            #    print ('You selected item %d: "%s"' % (index, value))
            if not value is None:
                self.selection = value

        def getMarkPage():
            if self.selection == "":
                messagebox.showinfo("Information", "Please select a Room")
            else:
                students = self.core.getStudents_room(self.selection)
                roomID = self.core.get_roomID(self.selection)
                self.markPage(students, roomID)

        # background_image = Image.open('EG_Logo.jpg')
        # background_image = background_image.resize((250, 250))
        # photo = ImageTk.PhotoImage(background_image)
        # z = Label(self.root, image=photo)
        # z.place(x=10, y=10)
        # z.image = background_image

        frame1 = Frame(self.root)
        frame1.pack()
        frame2 = Frame(self.root)
        frame2.pack()
        frame3 = Frame(self.root)
        frame3.pack(ipady=0)
        frame4 = Frame(self.root)
        frame4.pack()

        a = Label(frame1, text="R8ME", font="device 30 bold", height=2, fg="blue")
        admin_label = "Admin name: " + username
        b = Label(frame1, text=admin_label, font="device 15")
        d = Label(frame2, text="Available groups:", bd=20, font="ansi 20")


        i = Listbox(frame2, bd=2, selectmode=SINGLE, font="ansi 15")
        h = Button(frame2, text="Mark", font="system 20 bold", command=getMarkPage, fg="red")
        counter = 0

        for value in data['RoomDetails']:
            print("Value: ", value)
            i.insert(counter, value['RoomName'])
            counter += 1

        if counter > 0:
            i.activate(0)
        i.bind("<Double-Button-1>", showgroup)

        # e = Label(frame3, text="Selected group:", font="ansi 20")
        # e.pack(side=LEFT)


        a.pack()
        b.pack()
        d.pack(side=TOP)
        h.pack(side=RIGHT, padx=20)
        i.pack(side=RIGHT, ipadx=50, pady=40)

        self.root.mainloop()

    def studentPage(self):
        self.root.destroy()
        self.root = Tk()
        self.root.title("Student Page")

        def commentsubmitted():
            comment = g.get()
            j.insert(0, comment)
            self.root.messagebox.showinfo("Comment", "Your comment has been submitted")

        # background_image = Image.open('EG_Logo.jpg')
        # background_image = background_image.resize((300, 250))
        # photo = ImageTk.PhotoImage(background_image)
        # z = Label(self.root, image=photo)
        # z.place(x=10, y=10)
        # z.image = background_image

        y = Label(self.root, text="Total Mark:", font="device 30 bold")
        y.place(x=50, y=500)

        frame1 = Frame(self.root)
        frame1.pack()
        frame2 = Frame(self.root)
        frame2.pack()
        frame3 = Frame(self.root)
        frame3.pack(ipady=20)
        frame4 = Frame(self.root)
        frame4.pack()

        a = Label(frame1, text="R8ME", font="device 30 bold", height=2, fg="blue")
        b = Label(frame1, text="Admin name", font="device 15")
        d = Label(frame2, text="Your group members:", bd=20, font="ansi 20")
        e = Label(frame3, text="Add a comment:", font="ansi 20")
        f = Label(frame4, text="Comments:", font="ansi 20")

        g = Entry(frame3, bd=3)
        h = Button(frame3, text="Submit", font="system 20 bold", command=commentsubmitted)
        i = Listbox(frame2, bd=2)
        j = Listbox(frame4, bd=2, font="ansi 15")
        k = Scrollbar(j)

        a.pack()
        b.pack()
        d.pack()
        e.pack(side=LEFT)
        f.pack()

        h.pack(side=RIGHT, padx=20)
        g.pack(side=LEFT)
        i.pack(side=BOTTOM, ipadx=50)
        j.pack(ipadx=150, ipady=100)
        k.pack(side=RIGHT, fill=Y)

        self.root.mainloop()

    def markPage(self, students, roomID):
        if students != None:

            markpage = Tk()
            markpage.title("Mark Page")
            markpage.geometry("1000x800")

            def commentsubmitted():
                comment = g.get()
                j.insert(0, self.core.getUsername()+": "+comment)
                id = self.core.get_id()
                students = []
                data = self.core.get_data()


                self.core.commit_data(id, roomID, comment, "Comment")
                messagebox.showinfo("Comment", "Your comment has been submitted")

            def marksubmitted():
                userID = self.core.getID_username(self.selected_student)
                self.core.add_mark(userID, h.get())
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
            b = Label(frame1, text="Admin name: "+self.core.getUsername(), font="device 15")
            d = Label(frame2, text="Students:", bd=20, font="ansi 20")
            e = Label(frame3, text="Add a comment:", font="ansi 20")
            f = Label(frame4, text="Comments:", font="ansi 20")
            l = Button(frame3, text="Submit", font="system 20 bold", command=commentsubmitted)
            g = Entry(frame3, bd=3)

            i = Listbox(frame2, bd=2)

            print("Students: ", students)
            count = 0
            for k in students:
                i.insert(count, k)
                count += 1

            def showgroup(event):
                w = event.widget
                line = w.curselection()
                print(line)
                index = int(w.curselection()[0])
                value = w.get(index)
                print("Listbox slection: ", value)
                #    print ('You selected item %d: "%s"' % (index, value))
                if not value is None:
                    self.selected_student = value

            i.bind('<Double-Button-1>', showgroup)
            j = Listbox(frame4, bd=2)

            comments = self.core.getComments(roomID)
            counter = 0
            for com in comments:
                j.bind(counter, com)
                counter += 1

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
        else:
            messagebox.showinfo("Selection", "Nothing selected")
