> [!IMPORTANT]
> ### 🛑 THIS REPOSITORY IS RETIRED & ARCHIVED
> This project is no longer maintained or updated. The repository has been placed in a read-only state.
> The backend functionality of this system has been integrated into for the purposes of managing music promotion and playlists. The original intention of releasing this project commercially became untenable due to Spotify's tightening API restrictions and constantly changing schema. 

## Playlist Tools

Playlist tools is a site to manage Spotify playlist structures using the Spotify API written using Laravel, Vue.js & Intertia.js with a MySQL database.

It currently supports de-duplication of playlists, and removal of n number of old tracks from playlists (with the option to exclude selected artists or track names), based on stored user input.

These operations can be automated and scheduled to run hourly or daily at a specific time and on specific days.

It was originally inspired to help manage and automate aspects of playlists I run as a music enthusiast and musician. Plans were made to develop this into a commercial platform but tighter Spotify restrictions and frequently changing API schemas, meant this wasn't a viable option.

De-duplication for instance was removed as a native function from the Spotify API, so the process in the system therefore needs to scan the playlist ordering and mapping it, saving the most recent position for duplicates to get the most recent added version, and then calculating the track it should be inserted after. Then the API is called to delete all duplicate tracks and the playlist is recursively called and updated inserting one of the removed duplicate tracks at the correct position as identified by the mapped track to add after. The only downside to this is that the time added for these re-added duplicate tracks is not preserved meaning sort by date will then have changed, but there is no way around this.
