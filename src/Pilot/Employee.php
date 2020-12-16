<?php

namespace Flex360\Pilot\Pilot;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Spatie\Image\Manipulations;
use Flex360\Pilot\Pilot\Department;
use Spatie\MediaLibrary\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Flex360\Pilot\Pilot\Traits\UserHtmlTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Flex360\Pilot\Pilot\Traits\PilotTablePrefix;
use Flex360\Pilot\Pilot\Traits\PresentableTrait;
use Flex360\Pilot\Pilot\Traits\SocialMetadataTrait;
use Flex360\Pilot\Pilot\Traits\HasEmptyStringAttributes;

class Employee extends Model implements HasMedia
{
    use PresentableTrait,
        SocialMetadataTrait,
        UserHtmlTrait,
        HasMediaTrait,
        SoftDeletes,
        HasEmptyStringAttributes,
        PilotTablePrefix;

    protected $table = 'employee';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $emptyStrings = [
        'photo', 'first_name', 'last_name', 'start_date', 'birth_date', 'job_title', 'phone_number', 'extension',
         'email', 'office_location',
    ];

    public function registerMediaConversions(Media $media = null)
    {
        // let's always use standard names like thumb, xsmall, small, medium, large, xlarge
        $this->addMediaConversion('thumb')
        ->crop(Manipulations::CROP_TOP_RIGHT, 300, 300);

        $this->addMediaConversion('small')
            ->width(300)
            ->height(300);
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, config('pilot.table_prefix') . 'department_' . config('pilot.table_prefix') . 'employee')->orderBy('position');
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getPhotoAttribute($value)
    {
        $mediaItem = $this->getFirstMedia('photo');

        if (!empty($mediaItem)) {
            return $mediaItem->getUrl();
        }

        return $value;
    }

    public function getPhotoThumbAttribute($value)
    {
        $mediaItem = $this->getFirstMedia('photo');

        if (!empty($mediaItem)) {
            return $mediaItem->getUrl('thumb');
        }

        return $value;
    }

    public static function getSelectList()
    {
        return static::orderBy('last_name')
            ->get()
            ->prepend(['id' => '', 'fullName' => '[No Employee Selected]'])
            ->pluck('fullName', 'id');
    }

    public function duplicate()
    {
        $model = $this;

        $newModel = $model->replicate();

        // append to the title to designate a copy
        $newModel->first_name .= ' (Copy)';

        // copy media items
        foreach ($model->media as $media) {
            $media->copyTo($newModel);
        }

        // make new copy a draft
        $newModel->status = 10;

        $newModel->push();

        // copy all attached categories over to new model
        foreach ($model->departments as $cat) {
            $newModel->departments()->attach($cat);
        }

        return $newModel;
    }

    public function setStartDateAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['start_date'] = Carbon::createFromFormat('m-d-Y', $value);
        } else {
            $this->attributes['start_date'] = '';
        }
    }

    public function ignoreStartDateMutator($value) {
        $this->attributes['start_date'] = $value;
    }

    public function getStartDateAttribute($value)
    {
        if (!empty($value)) {
            $createdAt = Carbon::parse($value);
            return $createdAt->format('m-d-Y');
        } else {
            return '';
        }

    }

    public function getServiceLength()
    {
        $startDateOriginal = Carbon::createFromFormat('m-d-Y', $this->start_date);
        return str_replace(' ago', '', $startDateOriginal->diffForHumans());
    }

    public function setBirthDateAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['birth_date'] = Carbon::createFromFormat('m-d-Y', $value);
        } else {
            $this->attributes['birth_date'] = '';
        }
    }

    public function ignoreBirthDateMutator($value) {
        $this->attributes['birth_date'] = $value;
    }

    public function getBirthDateAttribute($value)
    {
        
        if (!empty($value)) {
            $createdAt = Carbon::parse($value);
            return $createdAt->format('m-d-Y');
        } else {
            return '';
        }

    }

    public static function getUpcomingBirthdays()
	{
		// start range 3 days ago
		$start = date('z') + 1 - 3;

		// end range 7 days from now
		$end = date('z') + 1 + 7;

		return Employee::whereRaw("DAYOFYEAR(birth_date) BETWEEN $start AND $end")
						->orderBy(\DB::raw('DAYOFYEAR(birth_date)'))
						->limit(5)
						->get();
	}

	public static function getUpcomingAnniversaries()
	{
		// start range 3 days ago
		$start = date('z') + 1 - 3;

		// end range 7 days from now
		$end = date('z') + 1 + 7;

		return Employee::whereRaw("DAYOFYEAR(start_date) BETWEEN $start AND $end")
						->orderBy(\DB::raw('DAYOFYEAR(start_date)'))
						->limit(5)
						->get();
	}

    public static function getStatuses()
    {
        return [
            10 => 'Draft',
            30 => 'Published'
        ];
    }

    public function getStatus()
    {
        $status = \Employee::getStatuses();

        return (object) [
            'id' => $this->status,
            'name' => $status[$this->status],
        ];
    }

    public function url()
    {
        return route('employee.index', [
            'employee' => $this->id,
            'slug' => $this->getSlug(),
        ]);
    }

    public function getUrlAttribute($value)
    {
        return $this->url();
    }

    public function getSlug()
    {
        return Str::slug($this->title);
    }
}
