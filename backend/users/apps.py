from django.apps import AppConfig


class UsersConfig(AppConfig):
    name = 'users'

    def ready(self):
        from django.conf import settings
        import mongoengine
        mongoengine.connect(
            db=settings.MONGO_DB_NAME,
            host=settings.MONGO_URI,
        )
