## Controls

This part allows you to define, modify and plan new controls.

### List of controls <a name="list"></a>

The list of controls allows you to display the list of controls, to filter them by domain or to search for a control based on part of its name.

[<img src="/deming/images/m1.png" width="600">](/deming/images/m1.png)

Clicking on :

* the domain, you arrive at the [chosen domain definition](/deming/config/#domain)

* the clause, you arrive on the [description of the control](#show)

* the date you arrive the [control planning](#plan) screen


### Show control <a name="show"></a>

This screen displays a control.

[<img src="/deming/images/m2.png" width="600">](/deming/images/m2.png)

A control consists of:

* of a domain;

* of a name;

* a security objective;

* attributes;

* controlment data:

* of the verification model;

* an indicator (green, orange, red); and

* an action plan to be applied if control of this control fails.

When you click:

* “Plan”: you arrive at the [control planning](#plan) screen.

* "Modify": you arrive at [the controlment modification screen](#edit)

* “Delete”: allows you to delete the control and return to [the list of controls](#list)

* "Cancel": returns you to the [list of controls](#list)


### Edit a control <a name="edit"></a>

This screen allows you to modify a control.

[<img src="/deming/images/m3.png" width="600">](/deming/images/m3.png)


When you click:

* "Save", the changes are saved and you return to the [control display](#show) screen.

* "Cancel", you return to the [control display](#show) screen.


### Schedule a control <a name="plan"></a>

This screen is used to plan a control based on a control.

The screen contains:

* The domain ;

* The name of the control;

* The security objective;

* The planning date; and

* The frequency of checks;

* Those responsible for carrying out the controls.

[<img src="/deming/images/plan.png" width="600">](/deming/images/plan.png)

When you click:

* "Plan",

     * If there is no control for this control, a new control is created by copying all the data from the control and is scheduled on the specified date.

     * If an unperformed measurement already exists for this measure, the planning date, periodicity and those responsible for performing the measurement are updated.

You then return to the [list of controls](#list).

* "Unplan", the security check associated with this control is deleted and the user returns to the [list of controls](#list).

* "Cancel", you return to the [list of controls](#list).
