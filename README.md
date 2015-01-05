tourist-train-pos
=================

This is a custom, PHP/MySQL point of sale system for a tourist
railroad. The pieces here are for education or copying: jewels, warts
and all.

Copyright (c) 2002-2004 Mitchell Perilstein
Licensed under GNU GPL v2.0.

## Important Notes

0. The state of PHP and accepted practice has advanced beyond this stuff substantially.  Some of this is 12 years old.
1. Just pieces here: it would take substantial effort to create a full running system in modern times.
2. Pieces have been omitted. See below.
3. The security model is way out of date, so this would be highly inappropriate for modern internet-facing deployment.
4. Porting to different railroads or conveyances was envisioned but might not be as easy as planned.

## What It Did

This system was written for a customer (now sanitzed) around 2002-2004
for a passenger tourist railroad with some interesting features for
the time.

* The PHP/MySQL site had a public view, allowing customers to book
  their own trips
* There were numerous employee (ticket agent) views, for booking,
  managing train stops and schedules, number of cars, etc.
* Agent views would show current train status, jumping to currently
  boarding train, etc.
* The train had several stations, with passengers making one way and
  round trips, including layovers.
* Advance reservation and walkup tickets were supported.
* Receipts and rollup accounting.
* A load factor was needed to avoid 100 percent seating; this was
  uncomfortable for passengers in groups.
* The physical layer included a number of remotely deployed and
  maintained Debain GNU/Linux kiosk systems configured as agent
  terminals.
* Agent terminals interfaced with ticket printers, and credit card
  swiping keyboards.
* Public key encryption, as well as SSL to the secure site, was used
  to store encrypted credit cards to charge in batches.

## What's Here

At the moment, core algorithms, templates, etc. I'm not including
Pear, PhpMyAdmin, Horde, or any of the third party components.  Some
code was cut and pasted from those components potentially, all GPL or
similar.  Encryption is omitted -- probably wickedly dangerous now as
written.

## TODO

* Sanitize and add more components from the system, currently omitted here.
** site maintenance functions
** custom kiosk hacks such as ticket printer interfacing to web browsers (!)

