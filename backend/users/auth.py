"""Custom JWT authentication that works with mongoengine User (no Django ORM)."""
import jwt
from datetime import datetime, timedelta
from django.conf import settings
from rest_framework.authentication import BaseAuthentication
from rest_framework.exceptions import AuthenticationFailed
from .models import User


def generate_tokens(user: User) -> dict:
    now = datetime.utcnow()
    access_payload = {
        'user_id': str(user.id),
        'username': user.username,
        'role': user.role,
        'exp': now + settings.JWT_ACCESS_LIFETIME,
        'iat': now,
        'type': 'access',
    }
    refresh_payload = {
        'user_id': str(user.id),
        'exp': now + settings.JWT_REFRESH_LIFETIME,
        'iat': now,
        'type': 'refresh',
    }
    key = settings.SECRET_KEY
    alg = settings.JWT_ALGORITHM
    return {
        'access': jwt.encode(access_payload, key, algorithm=alg),
        'refresh': jwt.encode(refresh_payload, key, algorithm=alg),
    }


def decode_token(token: str) -> dict:
    key = settings.SECRET_KEY
    alg = settings.JWT_ALGORITHM
    return jwt.decode(token, key, algorithms=[alg])


class MongoJWTAuthentication(BaseAuthentication):
    def authenticate(self, request):
        auth_header = request.headers.get('Authorization', '')
        if not auth_header.startswith('Bearer '):
            return None
        token = auth_header.split(' ', 1)[1]
        try:
            payload = decode_token(token)
        except jwt.ExpiredSignatureError:
            raise AuthenticationFailed('Token expired')
        except jwt.InvalidTokenError:
            raise AuthenticationFailed('Invalid token')

        if payload.get('type') != 'access':
            raise AuthenticationFailed('Invalid token type')

        try:
            user = User.objects.get(id=payload['user_id'])
        except User.DoesNotExist:
            raise AuthenticationFailed('User not found')

        if not user.is_active:
            raise AuthenticationFailed('User is inactive')

        return (user, token)
