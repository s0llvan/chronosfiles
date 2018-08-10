# ChronosFiles

Encrypted files hosting

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

- PHP >= 7.2
- Composer >= 1.2.2
- NodeJS >= 9

#### Debian 9

Install dependencies
```
apt install -y apt-transport-https lsb-release ca-certificates git wget zip curl
```

Add PHP GPG keys
```
wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
```

Add PHP repository
```
echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/php.list
```

Install PHP & Composer
```
apt update && apt install -y composer php7.2 php7.2-cli php7.2-sqlite3 php7.2-common php7.2-curl php7.2-gd php7.2-json php7.2-mbstring php7.2-mysql php7.2-opcache php7.2-readline php7.2-xml
```

Add NodeJS repository
```
curl -sL https://deb.nodesource.com/setup_9.x | bash -
```

Install NodeJS
```
apt-get install -y nodejs
```

### Installing

Clone the project repository
```
git clone https://github.com/s0llvan/ChronosFiles/
```

Install project dependencies

```
cd ChronosFiles && composer install && npm install
```

Generate database
```
php bin/console doctrine:schema:update --force
```

Generate fake data
```
php bin/console doctrine:fixtures:load
```

Run webpack

```
./node_modules/.bin/encore dev --watch
```

Run server

```
php bin/console server:run
```

## Built With

* [Symfony](https://symfony.com/doc/) - The web framework used


## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/s0llvan/ChronosFiles/tags). 

## Authors

* **s0llvan** - *Initial work* - [s0llvan](https://github.com/s0llvan)

See also the list of [contributors](https://github.com/s0llvan/ChronosFiles/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
