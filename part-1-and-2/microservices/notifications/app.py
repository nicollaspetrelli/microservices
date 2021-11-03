#!/usr/bin/env python
import json, pika, sys, os
from colorama import init, Fore, Back, Style

if __name__ == '__main__':
    init()

    if os.getenv('APP_RABBITMQ_USER') and os.getenv('APP_RABBITMQ_PASS'):
        credentials = pika.PlainCredentials(
            os.getenv('APP_RABBITMQ_USER'),
            os.getenv('APP_RABBITMQ_PASS')
        )
    else:
        credentials = null

    connection = pika.BlockingConnection(
        pika.ConnectionParameters(
            os.getenv('APP_RABBITMQ_HOST') or 'localhost',
            os.getenv('APP_RABBITMQ_PORT') or 5672,
            os.getenv('APP_RABBITMQ_VHOST') or '/',
            credentials
        )
    )

    channel = connection.channel()

    queue = os.getenv('APP_RABBITMQ_QUEUE')
    channel.queue_declare(
        queue=queue,
        passive=False,
        durable=True,
        exclusive=False,
        auto_delete=False
    )

    exchange = os.getenv('APP_RABBITMQ_EXCHANGE') or 'router'
    channel.exchange_declare(
        exchange=exchange,
        exchange_type='direct',
        passive=False,
        durable=True,
        auto_delete=False
    )

    channel.queue_bind(
        queue=queue,
        exchange=exchange
    )

    def callback(ch, method, properties, body):
        try:
            row = json.loads(body)
            print("%s%s%s New message %s" % (Style.NORMAL, Back.GREEN, Fore.WHITE, Style.RESET_ALL))
            print("%s%s To %s      User #%s" % (Style.NORMAL, Back.BLACK, Back.RESET, row['to']))
            print("%s%s Subject %s %s" % (Style.NORMAL, Back.BLACK, Back.RESET, row['subject']))
            print("%s%s Body %s    %s" % (Style.NORMAL, Back.BLACK, Back.RESET, row['body']))
            print("")
        except Exception as e:
            print(Fore.RED + "%r" % e.args)

    channel.basic_consume(queue=queue, on_message_callback=callback, auto_ack=True)

    print('Waiting for messages in queue "%s"' % queue)
    channel.start_consuming()
