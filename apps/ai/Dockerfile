FROM python:3.13-alpine

WORKDIR /app
COPY . .

RUN pip install flask

EXPOSE 5000
CMD ["python", "-m", "flask", "run", "--host=0.0.0.0"]
