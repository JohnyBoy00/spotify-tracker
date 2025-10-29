# Server Commands Quick Reference

## Starting the Development Server

### Recommended Method (with large file upload support)
```bash
./serve.sh
```
- Server URL: `http://localhost:8001`
- Upload limit: 25MB
- Execution time: 300 seconds
- Memory: 512MB

### Standard Method (basic PHP server)
```bash
php artisan serve
```
- Server URL: `http://localhost:8000`
- Upload limit: 2MB (default)
- Execution time: 30 seconds (default)

## Background Services

### For Listening Minutes Tracking
Run these in separate terminals:

```bash
# Terminal 1: Process background jobs
php artisan queue:work

# Terminal 2: Run scheduled tasks (auto-tracking)
php artisan schedule:work
```

## Stopping the Server

Press `Ctrl+C` in the terminal where the server is running.

## Troubleshooting

### Port Already in Use
If you see "Address already in use":
```bash
# Kill any running PHP servers
pkill -f "php.*artisan serve"
pkill -f "php.*-S localhost"

# Then start again
./serve.sh
```

### Large File Upload Fails
Make sure you're using `./serve.sh` instead of `php artisan serve`.

### Token Expired Errors
The system auto-refreshes tokens, but if you see errors:
```bash
# Re-authenticate by logging out and back in
# Visit: http://localhost:8001/logout
```

