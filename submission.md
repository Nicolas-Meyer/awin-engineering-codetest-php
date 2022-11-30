# Awin CoffeeBreak

## Outline
This is my attempt at refactoring the Awin CoffeeBreak. I tried to respect the time boundaries, though I probably spent more time on it than I should have.
I should mention that this was my first attempt at a Symfony based project - and definitely cost me some time. But I was keen to deal with the challenge. 

I toyed with the idea of restarting this from scratch. It would probably have been resulted in a better output, and faster. However, I also think this would have denatured the challenge. This was a bit of a minefield, and I probably would have missed a lot of the problems present in this code. 
## Approach
The first step I took was to make the code more readable - fixing some easy spelling mistakes, poorly named variables and obvious errors.

Then the idea was to be able to run this code - I did this through tests. I used mock repositories to simulate excepted data, and tried to leverage dependency injection to make this effective. This also allowed me to bypass the data storage problem, and not having to set this up was a big gain of time.

Then of course I had to make those tests pass, and be more meaningful. The lines are a bit blurred as it's hard to keep myself from fixing all the errors and mistakes that kept on popping up.

My main goal was trying to get the code to actually do what it was supposed to do (assuming I understood the intent). 
I would have loved to spend more time and refactoring itself, and try to achieve a smarter structure and way of working.

## Next steps
This is obviously in further need of refactoring before even thinking of going to production.


Changing the data structure would be very important in order to make the application scalable.
The preferences details could be stored independently. We should use enums instead of arrays of possible values.
The repositories would need to be updated. 
There is still a logic flaw in the way Preferences are retrieved (as they are not linked to the staff member they apply to).

Tests would need to be updated of course, made more robust and test for different scenarios.

Exceptions and errors should be reviewed and made more consistent. 

The HTML output should be completely reworked. It seems at odd with the other formats (and for a good reason, as you would expect json and xml format to be used by API calls and HTML to be read by a client). Ideally using a template engine. 

Before going to production, the product should also be fully setup of course - including routing and database migrations. It is not runnable at the moment, except for tests.

## Remarks
I'm looking forward to discuss all of this. I'm sure this will raise a lot of questions, and I'll be happy to explore all of this more in depth.
I do feel like the time constraint was a little short to actually get to a satisfying result!

## Manifest
- `Controller\CoffeeBreakPreferenceController`
- `Entity\CoffeeBreakPreference`
- `Entity\StaffMember`
- `Repository\CoffeeBreakPreferenceRepository`
- `Repository\StaffMemberRepository`
- `Services\Notifiers\Interfaces\NotifierInterface`
- `Services\Notifiers\EmailNotifier`
- `Services\Notifiers\SlackNotifier`
- `Tests\Services\EmamilNotifierTest`
- `Tests\Services\SlackNotifierTest`