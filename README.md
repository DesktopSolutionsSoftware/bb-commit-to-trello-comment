bb-commit-to-trello-comment
-----
BitBucket web hook for commenting commit messages into Trello cards.

## Installation

### Install via composer
```
composer create-project desktopsolutions/bbucket-to-trello
```
*...or clone the repository.*

### Run the config generator
```
php cli-install.php
```
*I was hoping to have this run automatically after the project installs but composer was forcing it through without allowing STDIN :(*

If you can't use the command-line tool, copy `config-example.php` to `config.php` and edit the values within.

## Set up the Web Hook
In your BitBucket repository go to settings -> web hooks. Create a new web hook with the URL of your installation. 

*ex. http://mysite.com/bbucket-to-trello*

##Use and Enjoy!
Put the URL of your relevant Trello card in your commit messages. The commit information will be added as a comment to that card (see below for an example).

You can reference as many cards as you would like within a single commit. Trello will parse the card URLs as usual so you can click them to jump between cards.

### Comment format
Commit `hash` by `author's display name`

---

`commit message`

---

`link to commit details here`

## Contributing
### Pull requests welcome :)

This was built for a specific use case. If you wish to extend any functionality of this program whether it be improving the comment messages, enhancing security, etc. please fork the repo and submit a pull request!

This project is maintained by [Andrew Natoli](https://github.com/AndrewNatoli).