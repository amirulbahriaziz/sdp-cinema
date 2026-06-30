<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\MovieReview;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Movies + their reviews. Mirrors app/mock/movies.json and movie-{1,2,3}.json so
 * the live API is byte-compatible with the mock data source.
 */
class MovieSeeder extends Seeder
{
    public function run(): void
    {
        $movies = [
            [
                'id' => 1,
                'title' => 'Venom: The Last Dance',
                'synopsis' => 'Eddie Brock and Venom are on the run. Hunted by both of their worlds and with the net closing in, the duo are forced into a devastating decision that will bring the curtain down on their symbiotic relationship.',
                'duration_min' => 109,
                'release_date' => '2026-06-12',
                'age_rating' => 'P13',
                'imdb_rating' => 7.1,
                'poster_url' => 'https://image.tmdb.org/t/p/w500/aosm8NMQ3UyoBVpSxyimorCQykC.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=__2bjWbetsB8',
                'genres' => ['Action', 'Sci-Fi', 'Adventure'],
                'casts' => ['Tom Hardy', 'Chiwetel Ejiofor', 'Juno Temple', 'Rhys Ifans'],
                'director' => 'Kelly Marcel',
                'writers' => ['Kelly Marcel', 'Tom Hardy'],
                'sections' => ['new_releases', 'popular'],
                'reviews' => [
                    ['user' => 'Sofia Rahman', 'rating' => 5, 'title' => 'Best of the trilogy', 'body' => 'The chemistry between Eddie and Venom carries the whole film. Action set pieces were wild.', 'created_at' => '2026-06-20T10:00:00+08:00'],
                    ['user' => 'Daniel Lim', 'rating' => 4, 'title' => 'Fun ride', 'body' => 'A bit messy in the middle but a satisfying send-off. Stay for the credits.', 'created_at' => '2026-06-21T14:30:00+08:00'],
                    ['user' => 'Aina Yusof', 'rating' => 3, 'title' => 'Okay-lah', 'body' => 'Enjoyable but predictable. The CGI was great though.', 'created_at' => '2026-06-22T19:12:00+08:00'],
                ],
            ],
            [
                'id' => 2,
                'title' => 'Dune: Part Two',
                'synopsis' => 'Paul Atreides unites with the Fremen while seeking revenge against the conspirators who destroyed his family. Facing a choice between the love of his life and the fate of the universe, he endeavors to prevent a terrible future.',
                'duration_min' => 166,
                'release_date' => '2026-05-28',
                'age_rating' => 'P13',
                'imdb_rating' => 8.5,
                'poster_url' => 'https://image.tmdb.org/t/p/w500/1pdfLvkbY9ohJlCjQH2CZjjYVvJ.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=Way9Dexny3w',
                'genres' => ['Sci-Fi', 'Drama', 'Adventure'],
                'casts' => ['Timothée Chalamet', 'Zendaya', 'Rebecca Ferguson', 'Javier Bardem'],
                'director' => 'Denis Villeneuve',
                'writers' => ['Denis Villeneuve', 'Jon Spaihts'],
                'sections' => ['popular', 'recommended'],
                'reviews' => [
                    ['user' => 'Hafiz Nordin', 'rating' => 5, 'title' => 'A masterpiece', 'body' => 'Visually stunning and emotionally gripping. Worth every ringgit on the big screen.', 'created_at' => '2026-06-01T20:00:00+08:00'],
                    ['user' => 'Mei Ling', 'rating' => 5, 'title' => 'Better than Part One', 'body' => 'The pacing and the worm-riding scene gave me chills. See it in IMAX if you can.', 'created_at' => '2026-06-03T11:45:00+08:00'],
                ],
            ],
            [
                'id' => 3,
                'title' => 'Inside Out 2',
                'synopsis' => 'Riley enters her teenage years and Headquarters undergoes a sudden demolition to make room for something new: brand-new Emotions. Joy, Sadness, Anger, Fear and Disgust must make space for Anxiety and friends.',
                'duration_min' => 96,
                'release_date' => '2026-06-19',
                'age_rating' => 'U',
                'imdb_rating' => 7.6,
                'poster_url' => 'https://image.tmdb.org/t/p/w500/vpnVM9B6NMmQpWeZvzLvDESb2QY.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=LEjhY15eCx0',
                'genres' => ['Animation', 'Family', 'Comedy'],
                'casts' => ['Amy Poehler', 'Maya Hawke', 'Kensington Tallman', 'Phyllis Smith'],
                'director' => 'Kelsey Mann',
                'writers' => ['Meg LeFauve', 'Dave Holstein'],
                'sections' => ['new_releases', 'recommended'],
                'reviews' => [
                    ['user' => 'Priya Suresh', 'rating' => 5, 'title' => 'Made me cry', 'body' => 'Anxiety is the best new character. A heartfelt look at growing up.', 'created_at' => '2026-06-22T16:00:00+08:00'],
                ],
            ],
            [
                'id' => 4,
                'title' => 'The Batman: Shadows',
                'synopsis' => 'A new threat rises over Gotham as the Dark Knight hunts a killer leaving cryptic clues across the city.',
                'duration_min' => 142,
                'release_date' => '2026-06-05',
                'age_rating' => '18',
                'imdb_rating' => 7.9,
                'poster_url' => 'https://image.tmdb.org/t/p/w500/74xTEgt7R36Fpooo50r9T25onhq.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'genres' => ['Action', 'Crime', 'Thriller'],
                'casts' => ['Robert Pattinson', 'Zoë Kravitz', 'Colin Farrell'],
                'director' => 'Matt Reeves',
                'writers' => ['Matt Reeves', 'Peter Craig'],
                'sections' => ['popular'],
                'reviews' => [
                    ['user' => 'Daniel Lim', 'rating' => 4, 'title' => 'Dark and gritty', 'body' => 'Moody noir Gotham done right. Slightly too long but gripping.', 'created_at' => '2026-06-10T21:00:00+08:00'],
                ],
            ],
            [
                'id' => 5,
                'title' => 'Furiosa: A Mad Max Saga',
                'synopsis' => 'As the world falls, young Furiosa is snatched from the Green Place of Many Mothers and falls into the hands of a great biker horde led by the Warlord Dementus. Sweeping through the Wasteland, she must survive and find her way home.',
                'duration_min' => 148,
                'release_date' => '2026-05-24',
                'age_rating' => '18',
                'imdb_rating' => 7.5,
                'poster_url' => 'https://image.tmdb.org/t/p/w500/iADOJ8Zymht2JPMoy3R7xceZprc.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=XJMuhwVlca4',
                'genres' => ['Action', 'Adventure', 'Sci-Fi'],
                'casts' => ['Anya Taylor-Joy', 'Chris Hemsworth', 'Tom Burke'],
                'director' => 'George Miller',
                'writers' => ['George Miller', 'Nico Lathouris'],
                'sections' => ['new_releases', 'popular'],
                'reviews' => [
                    ['user' => 'Hafiz Nordin', 'rating' => 4, 'title' => 'Ride eternal', 'body' => 'A worthy prequel. Anya is fierce and the action is relentless.', 'created_at' => '2026-05-28T20:00:00+08:00'],
                ],
            ],
            [
                'id' => 6,
                'title' => 'Gladiator II',
                'synopsis' => 'Years after witnessing the death of Maximus, Lucius is forced into the Colosseum after his home is conquered by the tyrannical emperors of Rome, and must find the strength to return glory to the people.',
                'duration_min' => 148,
                'release_date' => '2026-06-08',
                'age_rating' => '18',
                'imdb_rating' => 7.2,
                'poster_url' => 'https://image.tmdb.org/t/p/w500/2cxhvwyEwRlysAmRH4iodkvo0z5.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=H-Wkn7F4Q1c',
                'genres' => ['Action', 'Drama', 'Adventure'],
                'casts' => ['Paul Mescal', 'Denzel Washington', 'Pedro Pascal', 'Connie Nielsen'],
                'director' => 'Ridley Scott',
                'writers' => ['David Scarpa'],
                'sections' => ['popular'],
                'reviews' => [
                    ['user' => 'Hafiz Nordin', 'rating' => 4, 'title' => 'Are you not entertained?', 'body' => 'Denzel steals every scene. Spectacle done right.', 'created_at' => '2026-06-12T21:30:00+08:00'],
                ],
            ],
            [
                'id' => 7,
                'title' => 'Moana 2',
                'synopsis' => 'After an unexpected call from her wayfinding ancestors, Moana journeys to the far seas of Oceania and into dangerous, long-lost waters for an adventure unlike anything she has ever faced.',
                'duration_min' => 100,
                'release_date' => '2026-06-26',
                'age_rating' => 'U',
                'imdb_rating' => 6.9,
                'poster_url' => 'https://image.tmdb.org/t/p/w500/yh64qw9mgXBvlaWDi7Q9tpUBAvH.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=hDZ7y8RP5HE',
                'genres' => ['Animation', 'Family', 'Adventure'],
                'casts' => ["Auli'i Cravalho", 'Dwayne Johnson'],
                'director' => 'David Derrick Jr.',
                'writers' => ['Jared Bush', 'Dana Ledoux Miller'],
                'sections' => ['new_releases'],
                'reviews' => [
                    ['user' => 'Priya Suresh', 'rating' => 4, 'title' => 'The ocean is back', 'body' => 'Gorgeous animation and catchy songs. Kids loved it.', 'created_at' => '2026-06-28T15:00:00+08:00'],
                ],
            ],
            [
                'id' => 8,
                'title' => 'Deadpool & Wolverine',
                'synopsis' => 'A listless Wade Wilson toils away in civilian life. When his home world faces an existential threat, Wade must reluctantly suit up again with an even more reluctant Wolverine.',
                'duration_min' => 128,
                'release_date' => '2026-06-14',
                'age_rating' => '18',
                'imdb_rating' => 7.7,
                'poster_url' => 'https://image.tmdb.org/t/p/w500/8cdWjvZQUExUUTzyp4t6EDMubfO.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=73_1biulkYk',
                'genres' => ['Action', 'Comedy', 'Sci-Fi'],
                'casts' => ['Ryan Reynolds', 'Hugh Jackman', 'Emma Corrin'],
                'director' => 'Shawn Levy',
                'writers' => ['Ryan Reynolds', 'Rhett Reese', 'Paul Wernick'],
                'sections' => ['popular', 'recommended'],
                'reviews' => [
                    ['user' => 'Daniel Lim', 'rating' => 5, 'title' => 'Chaotic fun', 'body' => 'Endless cameos and laughs. Jackman and Reynolds are gold.', 'created_at' => '2026-06-18T22:00:00+08:00'],
                ],
            ],
        ];

        foreach ($movies as $data) {
            $reviews = $data['reviews'];
            unset($data['reviews']);

            $movie = Movie::create($data);

            foreach ($reviews as $r) {
                $reviewer = $this->reviewer($r['user']);
                MovieReview::create([
                    'movie_id' => $movie->id,
                    'user_id' => $reviewer->id,
                    'rating' => $r['rating'],
                    'title' => $r['title'],
                    'body' => $r['body'],
                    'created_at' => $r['created_at'],
                    'updated_at' => $r['created_at'],
                ]);
            }
        }
    }

    /** Find-or-create a reviewer user keyed by display name. */
    private function reviewer(string $name): User
    {
        $email = Str::slug($name).'@reviewer.sdpcinema.test';

        return User::firstOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => Hash::make('password')],
        );
    }
}
