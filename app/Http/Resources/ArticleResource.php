<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Flatten all attributes to key => value form
        $flatAttributes = collect($this->attributes ?? [])
            ->mapWithKeys(fn ($item) => [$item['key'] => $item['value']])
            ->toArray();

        // Default blank groups to prevent undefined variable errors
        $people = [];
        $financial = [];
        $other_names = [];

        /** ----------------------------------------------------
         * 👤 Company-specific grouping (people)
         * --------------------------------------------------- */
        if ($this->category?->name === 'company') {
            $people = collect($flatAttributes)
                ->only([
                    'founder', 'owners', 'members', 'number_of_employees',
                    'parent', 'predecessors', 'successors', 'key_people',
                ])
                ->filter()
                ->toArray();
        }

        if ($this->category?->name === 'games') {
            $people = collect($flatAttributes)
                ->only([
                    'manufacturers', 'designers', 'illustrators', 'actors',
                    'voice_actor', 'pulisher', 'number_of_players',
                ])
                ->filter()
                ->toArray();
        }

        /** ----------------------------------------------------
         * 💰 Financial grouping
         * --------------------------------------------------- */
        $financial = collect($flatAttributes)
            ->only([
                'services', 'rating', 'revenue', 'revenue_year',
                'operating_income', 'income_year',
                'net_income', 'net_income_year', 'profit',
            ])
            ->filter()
            ->toArray();

        /** ----------------------------------------------------
         * 🔤 Other name variations
         * --------------------------------------------------- */
        $other_names = collect($flatAttributes)
            ->only([
                'former_names', 'trading_names',
                'native_name', 'native_name_lang', 'romanized_name',
            ])
            ->filter()
            ->toArray();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'url' => config('app.frontend_url').'/'.$this->slug,
            'slug' => $this->slug,
            'language_code' => $this->language_code ?? 'en',
            'namespace' => $this->namespace ?? 'Main',
            'is_minor' => $this->is_minor ?? false,
            'is_bot' => $this->is_bot ?? false,
            'language' => $this->whenLoaded('language', fn () => [
                'code' => $this->language->code,
                'name' => $this->language->name,
                'native_name' => $this->language->native_name,
                'direction' => $this->language->direction,
            ]),
            'available_languages' => $this->getAvailableLanguages(),
            'category' => new CategoryResource($this->category),

            /** ------------------------------------------------
             * Creator
             * ------------------------------------------------*/
            'creator' => $this->whenLoaded('creator', fn () => [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
                'email' => $this->creator->email,
            ]),

            /** ------------------------------------------------
             * Flat key-value attributes
             * ------------------------------------------------*/
            'attributes' => $flatAttributes,

            /** ------------------------------------------------
             * Occupation
             * ------------------------------------------------*/
            'occupation' => $this->whenLoaded('occupation', fn () => [
                'occupation' => $this->occupation->occupation,
                'employer' => $this->occupation->employer,
                'organization' => $this->occupation->organisation,
                'title' => $this->occupation->title,
                'years_active' => $this->occupation->years_active,
                'location' => $this->occupation->location,
                'agent' => $this->occupation->agent,
                'known_for' => $this->occupation->known_for,
                'notable_works' => $this->occupation->notable_works,
                'home_town' => $this->occupation->home_town,
                'salary' => $this->occupation->salary,
                'net_worth' => $this->occupation->net_worth,
                'television' => $this->occupation->television,
            ]),

            /** ------------------------------------------------
             * Politician
             * ------------------------------------------------*/
            'politician' => $this->whenLoaded('politician', fn () => [
                'term' => $this->politician->term,
                'party' => $this->politician->party,
                'movement' => $this->politician->movement,
                'predecessor' => $this->politician->predecessor,
                'successor' => $this->politician->successor,
                'opponents' => $this->politician->opponents,
                'awards' => $this->politician->awards,
                'honors' => $this->politician->honors,
            ]),

            /** ------------------------------------------------
             * Family
             * ------------------------------------------------*/
            'family' => $this->whenLoaded('family', fn () => [
                'spouse' => $this->family->spouse,
                'children' => $this->family->children,
                'domestic_partner' => $this->family->domestic_partner,
                'parents' => $this->family->parents,
                'family' => $this->family->family,
                'relatives' => $this->family->relatives,
            ]),

            /** ------------------------------------------------
             * Grouped Attributes
             * ------------------------------------------------*/
            'people' => $people,
            'financial' => $financial,
            'other_names' => $other_names,

            /** ------------------------------------------------
             * Social links
             * ------------------------------------------------*/
            'links' => $this->whenLoaded('socialLinks', function () {
                return $this->socialLinks->map(fn ($link) => [
                    'platform' => $link->key,
                    'url' => $link->value,
                ]);
            }),

            'created_at' => formatDateTime($this->created_at),
            'updated_at' => formatDateTime($this->updated_at),
        ];
    }
}
