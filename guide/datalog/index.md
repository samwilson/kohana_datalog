DataLog module for ORM
======================

This module is for keeping a log of changes made to ORM data.
When added to an ORM model, it records who made what changes, when.

It stores actual values of the data (and usernames)
rather than references to other parts of the database
so that it can be as simple and robust as possible.
This way it can be used with schema changes over time
and still keep historical data.
