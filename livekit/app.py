import os

from flask import Flask, request, jsonify

from flask_cors import CORS

from dotenv import load_dotenv

from livekit.api import AccessToken, VideoGrants, TokenVerifier, WebhookReceiver
 
# Load environment variables from .env file

load_dotenv()
 
# Fetch environment variables

SERVER_PORT = os.environ.get("SERVER_PORT")

LIVEKIT_API_KEY = os.environ.get("LIVEKIT_API_KEY")

LIVEKIT_API_SECRET = os.environ.get("LIVEKIT_API_SECRET")
 
# Initialize Flask app

app = Flask(__name__)
 
# Enable Cross-Origin Resource Sharing (CORS)

CORS(app)
 
# Health Check route

@app.get("/health")

def health_check():

    return jsonify({"status": "OK", "message": "Service is running"}), 200
 
# Create the token for room access

@app.post("/token")

def create_token():

    room_name = request.json.get("roomName")

    participant_name = request.json.get("participantName")
 
    # Validate input parameters

    if not room_name or not participant_name:

        return jsonify({"errorMessage": "roomName and participantName are required"}), 400
 
    # Create an access token for the participant with the given room

    token = (

        AccessToken(LIVEKIT_API_KEY, LIVEKIT_API_SECRET)

        .with_identity(participant_name)

        .with_grants(VideoGrants(room_join=True, room=room_name))

    )

    # Return the generated token in the response

    return jsonify({"token": token.to_jwt()})
 
# Initialize TokenVerifier and WebhookReceiver for LiveKit webhook validation

token_verifier = TokenVerifier(LIVEKIT_API_KEY, LIVEKIT_API_SECRET)

webhook_receiver = WebhookReceiver(token_verifier)
 
# Endpoint to handle LiveKit webhooks

@app.post("/ensembler/livekit/webhook")

def receive_webhook():

    # Get the Authorization header from the request

    auth_token = request.headers.get("Authorization")
 
    # Check if the Authorization header is present

    if not auth_token:

        return jsonify({"errorMessage": "Authorization header is required"}), 401
 
    try:

        # Decode and verify the webhook event using the Authorization token

        event = webhook_receiver.receive(request.data.decode("utf-8"), auth_token)

        print("LiveKit Webhook:", event)

        # Process the event (you can add more logic here if needed)

        # Example: Trigger actions based on event (e.g., log, update DB)

        # Respond with a successful receipt of the event

        return "ok"

    except Exception as e:

        # If verification fails, print the error and respond with a 401 status

        print(f"Error processing webhook: {str(e)}")

        return jsonify({"errorMessage": "Authorization header is not valid"}), 401
 
# Main entry point for running the Flask app

if __name__ == "__main__":

    app.run(debug=True, host="0.0.0.0", port=SERVER_PORT)

 