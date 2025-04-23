## README

# Setup Instructions

1. **Copy the Environment File**

   Copy the example environment file to create your own environment configuration:
   
   ```sh
   cp .env.example .env
   ```

2. **Run the Setup Script**

   After copying the environment file, run the following script to complete the setup:
   
   ```sh
   ./run.sh
   ```

This will configure your environment and start the application as needed.

## Data Sync

To manually sync and fetch pornstar data, run the following command:

```sh
docker exec -it pornstar_api php artisan pornstars:dispatch-fetch
```

---

## API Endpoints

The following API endpoints are available:
Runs on port :8000
### List Pornstars
- **GET** `/api/v1/pornstars`
  - Returns a paginated list of pornstars.
  - Query params:
    - `page` (int, optional): Page number
    - `per_page` (int, optional): Results per page
    - `sort` (string, optional): Sort by field (e.g., name)
    - `filter` (array, optional): Filter by attributes (e.g., hair_color, ethnicity)

### Search Pornstars
- **GET** `/api/v1/pornstars/search`
  - Search for pornstars by query and filters.
  - Query params:
    - `q` (string, optional): Search query (min 2 chars)
    - `filter` (array, optional): Filter by attributes (e.g., hair_color, ethnicity, age, height, weight, videos, views, rank)
    - `page`, `per_page`, `sort` (see above)

### Get Pornstar Details
- **GET** `/api/v1/pornstars/{id}`
  - Returns detailed info for a specific pornstar by ID.
  - Query params:
    - `fields` (string, optional): Comma-separated fields to include
    - `include` (string, optional): Comma-separated relations (e.g., thumbnails, attributes)

All endpoints return JSON responses.

---
