# Projects

## Full Stack Project - School of Computer Science TA Management Website

Build a new  School of Computer Science TA  Management website. 
Design a professional looking website: **responsive, interactive, functional** and **pretty**.  

Based on users' types (students, professors, TA administrators, teaching assistants and system operators), users can use multiple functionalities, for instance, students can rate teaching assistants in their courses or system operators can manage users. 

Technology Stack

- Frontend: HTML5/CSS, JavaScript
- Backend: PHP
- Database: SQLite3

Worked with Yining Wang and Xinyue Wei as a team of three. 



## Midwifery Database Application

Developed a **Java** application to be used by the Midwifery program run by the Quebec Ministry of Health. This program allows pregnant couples to receive supports and basic health care services through a personalized midwife (Sgae-femme). 

- Step 1: design a model.
- Step 2: create a schema and implement a database using **DB2**, poputlate the database with data, maintain, query and update data using **IntelliJ IDEA IDE**. 
- Step 3: devlop application programs, and implement a user-friendly interface with **JDBC**. 



ER model:

![ER model](https://github.com/nzhou15/Projects/blob/main/images/ER.pdf)


## Data Analytics of Discussions Around a Recently-released Movie

###### Overview

Your team has been hired by a media company that wants to understand the discussions currently happening around the film “(insert a recently-released movie that your team selected here)”. They have indicated that they are especially concerned with the favorability of the audience response. Specifically, they want to know:

1. The salient topics discussed around their film and what each topic primarily concerns
2. Relative engagement with those topics
3. How positive/negative the response to the movie has been



[Shang-Chi Movie Twitter Sentiment Analysis](https://github.com/zhenglinxiao/comp598_finalproject)

The analysis was draw on Twitter posts (tweets). Collected and filtered out 1,000 tweets that highly-related to the movie within a 3 day window using **Python** with **Pandas** and **NumPy** libraries through **Tweepy API**.

Analyzed the salient topics by computing **tf-idf scores**, the relative engagement and the sentiments with those topics. Visualized results and reported using **Matplotlib** library. 

Worked well in **a team of 3** and **independently**. 



## Goal-oriented game AI within Unity3D/C#

Implement the **AI system** entirely from scratch without using any built-in or external implementations, assets, or tools for game AI. 

The terrain is populated with 5 squirrels, each of which is controlled by its own **goal-oriented** AI system. Each squirrel is associated with (and initially spawned at) a unique tree (its home tree). Squirrels generally explore and exhibit some kind of simple idle behaviour, gather food (either nuts or whatever they find in garbage cans) which they bring back to their home tree, and practice self-preservation by running away from a player who gets too close. 

The player can impact the AI since squirrel’s run away. Pressing the space-bar should toggle the player in/out of a ghost-mode, during which the squirrels ignore the player. Thus, AI needs to re-plan to acheive the original goal. 

