import os
import mongoengine
from django.conf import settings
from django.core.wsgi import get_wsgi_application

os.environ.setdefault('DJANGO_SETTINGS_MODULE', 'oes.settings')

# Bootstrap Django first so settings are available
application = get_wsgi_application()

# Connect to MongoDB
mongoengine.connect(
    db=settings.MONGO_DB_NAME,
    host=settings.MONGO_URI,
)
