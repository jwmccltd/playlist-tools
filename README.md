## Playlist Tools

Playlist tools is a site to manage Spotify playlist structures written with Laravel and Vue.js.

It currently supports de-duplication of playlists and removal of n number of old tracks from playlists.

These operations can be automated and scheduled to run hourly or daily at a specific time and on specific days.

Because Spotify are basically bastards, this project will not be launched beyond personal use as the scope for being granted extended API usage requires (since May 2025) that the application is owned by a company not an individual, and that they must have at least 2500 active users. How this is possible, when the basic quota only grants 25 spotify users is beyond me. 

Also because Spotify are basically bastards, the de-duplication process is much more complicated than it used to be (it used to be possible to call the API with unique playist item id parameters to remove individual items. This has, for reasons I cannot possibly fathom, been removed, and is now done using URI's meaning that removing one duplicate removes them all). 

The process in the system therefore needs to scan the playlist ordering and mapping it, saving the most recent position for duplicates to get the most recent added version, and then calculating the track it should be inserted after. Then the API is called to delete all duplicate tracks and the playlist is recursively called and updated inserting one of the removed duplicate tracks at the correct position as identified by the mapped track to add after. The only downside to this is that the time added for these re-added duplicate tracks is not preserved meaning sort by date will then have changed, but there is no way around this.

The site populates a JSON structure via a seeder and this is recursively handled and processed by the front end allowing for a simple and fast mechanism to add new configurations and tidy front end code without the need for multiple components per configuration.