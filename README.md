# Symfony Bounded Context Microservice Stryle

This is a minimal Symfony distribution
to implement a bounded context inside a micro-service architecture.

## The Story

The Development Team have identified the following context inside the application domain:
It only contains the following bundles:

- PostContext: allow to insert and delete messages into a Channel
- ChannelContext: allow to create and delete Channels
- ChannelAuthorizationContext: allow to maintain the authorizations to write into a Channel
- IdentityAndAccessContext: allow to authenticate a user
- UserContext: allow a user to change the basic profile information

After started with  monolithic solution, the Team have decided to split
the application into 5 "Micro"Service (so far).

Each Bounder Context will be a separate application, with a separate database and
independently deployable.

The codebase was entirely developed with Symfony.
The team decided to keep Symfony Framework for the PostContext, UserContext and ChannelAuthorizationContext.
They have also decided to change the programming language for the ChannelContext, the ChannelAuthorizationContext
and the IdentityContext (which are the more "used" services)

The Team have identified also another service, called API-Proxy, which will be the gateway between the clients and the
rest of the services.
The API-Proxy adds also a security layer, by identifying each request.

The rest of the services will be into a private network.

## The Post Context
A publisher can publish and delete messages on a Channel.
The publisher can publish on a Channel only if he's authorized.
The publisher can delete only own messages.
A message in a channel has a creational date.
The publisher cannot publish or delete messages on a closed Channel.

###Microservices consideration
The team have already shipped part of the domain code, but they started wondering about the introduction
of the network.

- What's happen if the IdentityAndAccessContext it's not available?
- What's happen if the ChannelAuthorizationContext is not available or there is a not expected response?
- What's happen if the ChannelContext is not available or there is a not expected response?

They started with talk with the product owners and the have identified this scenarios:

- We don't really need caring about the IdentityAndAccessContext, because the authentication layer is given by
 the API-Proxy. We can take the publisherId from the request header, and it's enough. Note that, since the monolithic repo,
 in the PostContext we don't care about the user information like username, date of birth or whatever.
 All what we need about the Publisher is only the PublisherId.

- If we're not able to determinate if a user is authorized to publish or not into a channel, then show to the user a message where he's
informed that is not possible to publish on this channel in this moment.

- If we're not able to determinate if a channel exists and is not closed, then show to the user a message where he's
informed that is not possible to publish on this channel in this moment.

###Integrations

The PostContext should be integrated with:

- ChannelContext to get the information of the channel
- ChannelAuthorizationContext to determinate if a Publisher can publish or not messages into a Channel.

### Future Integrations
The ChannelContext needs to answer a simple question:
- Give me all the messages into a Channel.

The PostContext needs to give to the ChannelContext this information.
This API should be really fast. To avoid a direct integration between the ChannelContext -> PostContext
the team decides to push into a queue the a message straightway later it has been published.
The ChannelContext can consume this queue and update his ReadModel to perform this query really fast.

##Symfony implementation
The implementation of the PostContext hs been done with Symfony.
It has been developed with a Symfony minimal distribution inspired by:
- http://www.whitewashing.de/2014/10/26/symfony_all_the_things_web.html
- https://github.com/beberlei/symfony-minimal-distribution

##Installation

- Cloning the project:
```bash
git clone git@github.com:danieledangeli/symfony-microservice-bounded-context-example.git
```
- Install dependencies
```bash
composer install
```

- Setting environment variables

This vars needs to be exported in the environment:

    SYMFONY_ENV=dev
    SYMFONY_DEBUG=1
    SYMFONY__SECRET=abcdefg
    SYMFONY__MONOLOG_ACTION_LEVEL=debug
    SYMFONY__AUTH_SERVICE=http://localhost:8080
    SYMFONY__CHANNEL_SERVICE=http://localhost:8080
    SYMFONY__RABBIT_URL=http://localhost:8080
    SYMFONY__RABBIT_PORT=http://localhost:8080

Or we can just add these variables in a file called .env in the prohect root directory

##Running the tests

Phpunit:
```bash
./bin/phpunit
```

Behat:
```bash
./bin/behat
```

By Makefile:

```bash
make tests
```