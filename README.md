```
1. POST /api/v1/add
```
Request
```
{
    "events": [
        {
            "lang": "русский",
            "sport": "футбол",
            "league": "Лига чемпионов УЕФА",
            "team1": "Реал",
            "team2": "Барселона",
            "date": "2019-01-01 10:01:01",
            "source": "sportdata.com"
        },
        {
            "lang": "русский",
            "sport": "футбол",
            "league": "Лига чемпионов УЕФА",
            "team1": "Реал",
            "team2": "Барселона",
            "date": "2019-01-01 13:00:00",
            "source": "sportdata2.com"
        },
        {
            "lang": "русский",
            "sport": "футбол",
            "league": "Лига чемпионов УЕФА",
            "team1": "Реал",
            "team2": "Барселона",
            "date": "2019-01-02 10:00:00",
            "source": "sportdata3.com"
        },
        {
            "lang": "русский",
            "sport": "футбол",
            "league": "Лига чемпионов УЕФА",
            "team1": "Реал",
            "team2": "Барселона",
            "date": "2019-01-02 10:00:00",
            "source": "sportdata4.com"
        }
    ]
}
```
Response
```
{
    "success": 1
}
```

-------

```
2. GET /api/v1/random
```

Request
```
GET /api/v1/random?source=sportdata4.com
GET /api/v1/random?start=2019-01-01 01:00:00&end=2019-01-02 10:00:00
GET /api/v1/random?source=sportdata4.com&start=2019-01-01 01:00:00&end=2019-01-02 10:00:00
```

Response
```
{
    "game": {
        "lang": "русский",
        "sport": "футбол",
        "league": "Лига чемпионов УЕФА",
        "team1": "Реал",
        "team2": "Барселона",
        "date": "2019-01-02 10:00:00"
    },
    "buffers": [
        {
            "lang": "русский",
            "sport": "футбол",
            "league": "Лига чемпионов УЕФА",
            "team1": "Реал",
            "team2": "Барселона",
            "date": "2019-01-01 13:00:00",
            "source": "sportdata2.com"
        },
        {
            "lang": "русский",
            "sport": "футбол",
            "league": "Лига чемпионов УЕФА",
            "team1": "Реал",
            "team2": "Барселона",
            "date": "2019-01-02 10:00:00",
            "source": "sportdata3.com"
        },
        {
            "lang": "русский",
            "sport": "футбол",
            "league": "Лига чемпионов УЕФА",
            "team1": "Реал",
            "team2": "Барселона",
            "date": "2019-01-02 10:00:00",
            "source": "sportdata4.com"
        },
        {
            "lang": "русский",
            "sport": "футбол",
            "league": "Лига чемпионов УЕФА",
            "team1": "Реал",
            "team2": "Барселона",
            "date": "2019-01-01 10:01:01",
            "source": "sportdata.com"
        }
    ]
}
```