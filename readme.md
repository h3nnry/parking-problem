Write an Application that solves one or more of the following problems:


##Parking Problem

You are a software developer at a company named "Parking Solutions".

A client asks you to develop an Application for a shopping mall parking lot that has **N** total parking spaces, **X** entries and only 1 exit. There will be one barrier for each entry/exit.

The Application must satisfy the following needs: 
 
 * the total number of cars inside the parking lot must be monitored so that at any time, it can be queried
 * if there are no parking spaces available, the entry barriers will not open and a message will be shown explaining that the parking lot is full
 * there must not be more than **N** cars in the parking lot at the same time
 
> Notes:
> 
> * There are 2 actions that a car can make:
>   * entry
>   * exit


##Pollution Problem

Following legal issues, a "No Pollution" policy is requested that states the following:

* given a buffer percentage **B** there must not be more than **B**% cars of the total **N** number of parking spaces
inside the parking lot with their engine running
* the number of cars inside the parking lot that haven't parked yet (having their engine running) must be monitored so that at any time, it can be queried
 
> Notes:
> 
> * Two additional car actions are added:
>   * park
>   * un-park (leave the parking space but do not exit yet)
> * We assume that once a car has parked, it's engine has stopped


##Communication problem

If the parking Application shuts down or malfunctions, the barriers will fail to communicate with it and all of them
will remain closed, preventing any cars to enter or worse, to exit.

* Describe how would you treat this kind of issue
* Propose an implementation for solving the problem
* Integrate the implementation in your Application

-

###Overall Technical Requirements

* The Application should be integrated easily with other systems, regardless of communication requirements.
* Scalability is a plus
